<?php

namespace Magebees\Testimonial\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends Action
{
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * Testimonial Model Factory
     * @var \Magebees\Testimonial\Model\TestimonialcollectionFactory
     */
    protected $_testimonialFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    
    /**
     * Save Post Data
     */
    protected $_data;
    

    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magebees\Testimonial\Model\TestimonialcollectionFactory $testimonialFactory,

                $data = []
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_date = $date;
        $this->_testimonialFactory = $testimonialFactory;
    }
    
    /**
     * Post Inquiry
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        
            $data=$this->getRequest()->getPost()->toArray();
            $filedata=$this->getRequest()->getFiles()->toArray();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$data) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
            
            $captcha =  $this->getRequest()->getParam("captcha");
            $captcha_code =  $this->getCaptchaSession();
        if (!empty($captcha) && $captcha != $captcha_code) {
            $this->messageManager->addError(__('Captcha code does not match!'));
            $this->setTestimonialDataSession($data); // set form data to session
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
            $model = $this->_objectManager->create('Magebees\Testimonial\Model\Testimonialcollection');
            $file_driver= $this->_objectManager->get('\Magento\Framework\Filesystem\Driver\File');
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                
                $data['stores'] = $this->_storeManager->getStore()->getId();
                $data['inserted_date'] = $this->_date->date();
                $data['updated_date'] = $this->_date->date();
        if (isset($filedata['image']['name']) && $filedata['image']['name'] != '') {
            try {
                $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' =>'image']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('testimonial/images'));
                unset($result['tmp_name']);
                unset($result['path']);
                $data['image'] = $result['file'];
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Please Select Valid Image File'));               
                 $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                  $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
                return $resultRedirect;
                $data['image'] = $filedata['image']['name'];
            }
        }
                $approve_testimonial=$this->_scopeConfig->getValue('testimonial/frontend_settings/approve_testimonial', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($approve_testimonial) {
                $data['status']=2;
        }
                $model->setData($data);
                $model->save();
                $this->_data = $data;
                $enable_send_mail=$this->_scopeConfig->getValue('testimonial/admin_email_settings/enable_mail_post', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enable_send_mail) {
                $this->sendAdminEmail();
        }
                
                $submission_msg=$this->_scopeConfig->getValue('testimonial/frontend_settings/submission_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $this->messageManager->addSuccess(__($submission_msg));
                $this->_redirect('testimonial/index/index');
    }
    public function setTestimonialDataSession($post)
    {
        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
        return $session->setTestimonialFormData($post);
    }
    
    public function getCaptchaSession()
    {
        
        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
        return $session->getTestimonialCaptcha();
    }
    
    public function sendAdminEmail()
    {
        $data = $this->_data;
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);
        $post = $this->getRequest()->getPostValue();
        $testimonial_content=$post['testimonial'];
        $helper = $this->_objectManager->get('\Magebees\Testimonial\Helper\Data');
        $send_to=$this->_scopeConfig->getValue('testimonial/admin_email_settings/send_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $send_email=$this->_scopeConfig->getValue('testimonial/admin_email_settings/admin_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($send_to=='custom') {
            $owner_email=$send_email;
        } else {
            $owner_email=$this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
                
        $owner_name = $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $client_email = $post['email'];
        $subject = $this->_scopeConfig->getValue('testimonial/admin_email_settings/admin_email_subject', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $client_name = $post['name'];
        
        if (!$post) {
            $this->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
            $this->getResponse()->sendResponse();
            return;
        }
        $template = $this->_scopeConfig->getValue('testimonial/admin_email_settings/post_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->inlineTranslation->suspend();
        try {
            $this->_transportBuilder
             ->setTemplateIdentifier(
                 $template
             )->setTemplateOptions(
                 [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                 ]
             )->setTemplateVars(
                 [
                    'store' => $this->_storeManager->getStore(),
                    'owner_name' => $owner_name,
                    'client_name' => $client_name,
                    'data' => $postObject,
                    'subject'=>$subject
                    
                 ]
             )->setFrom(
                 [
                    'email' => $client_email,
                    'name' => $client_name
                 ]
             )
            ->addTo(
                $owner_email,
                $owner_name
            );
            
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }
}
