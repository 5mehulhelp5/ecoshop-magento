<?php

namespace Magebees\Testimonial\Controller\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    const XML_PATH_CLIENT_EMAIL_TEMPLATE = 'testimonial_client_email_settings_approve_template';
	protected $_date;
	protected $inlineTranslation;
	protected $_transportBuilder;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
    
        $this->_date = $date;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }
    public function execute()
    {
        $data=$this->getRequest()->getPost()->toArray();
        $model = $this->_objectManager->create('Magebees\Testimonial\Model\Testimonialcollection');
        $file_driver= $this->_objectManager->get('\Magento\Framework\Filesystem\Driver\File');
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
        $filedata=$this->getRequest()->getFiles()->toArray();
     if ($data) {
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
                    $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('testimonial_id')]);
                    return;
                }
            } else {
                if (isset($data['image'])) {
                    $new_image=str_replace("testimonial/images", "", $data['image']['value']);
                    $trim_img=trim($new_image);
                    if (isset($data['image']['delete'])) {
                        $del_img=$data['image']['delete'];
                        if ($del_img==1) {
                            $val=$mediaDirectory->getAbsolutePath('testimonial/images');
                            $file_driver->deleteFile($val.$trim_img);
                            $data['image'] ="";
                        }
                    } else {
                        $new_image=str_replace("testimonial/images", "", $data['image']['value']);
                        $data['image']=$trim_img;
                    }
                }
            }
            $id = $this->getRequest()->getParam('id');
            
            if (isset($data['testimonial_id'])) {
                $edit_data=$model->load($data['testimonial_id']);
                $status=$edit_data->getData('status');
                $data['updated_date']=$this->_date->date();
            } else {
                $data['updated_date']=$this->_date->date();
                $data['inserted_date']=$this->_date->date();
            }
                        
           
            if (isset($data['image'])) {
                $new_image=str_replace("testimonial/images", "", $data['image']);
                $model->setData('image', trim($new_image));
            }
            if (isset($data['stores'])) {
                 if (in_array('0', $data['stores'])) {
                    $data['stores'] = '0';
                } else {
                    $data['stores'] = implode(",", $data['stores']);
                }
            //unset($data['stores']);
            }
			if(empty($data['stores'])) {
					 $data['stores'] = '0';
			}
			
            $model->setData($data);
            
            try {
                $model->save();
                if (isset($data['testimonial_id'])) {
                    if ($status!=2) {
                        if ($data['status']==2) {
                            $scopeConfig=$this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
                            $client_email_config=$scopeConfig->getValue('testimonial/client_email_settings', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($client_email_config['enable_mail_approve']) {
                                $this->sendApproveEmail();
                            }
                        }
                    }
                }
                $this->messageManager->addSuccess(__('The Record has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Record.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    public function sendApproveEmail()
    {
        
        $post=$this->getRequest()->getPost()->toArray();
        $storeManager= $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $scopeConfig=$this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $client_email_config=$scopeConfig->getValue('testimonial/client_email_settings', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $owner_email=$scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                
        $owner_name = $scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $client_email = $post['email'];
        $subject = $client_email_config['client_email_subject'];
        $client_name = $post['name'];
        if (!$post) {
            $this->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
            $this->getResponse()->sendResponse();
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $this->_transportBuilder
             ->setTemplateIdentifier(
                 $client_email_config['approve_template']
             )->setTemplateOptions(
                 [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeManager->getStore()->getId(),
                 ]
             )->setTemplateVars(
                 [
                    'store' => $storeManager->getStore(),
                    'name' => $client_name,
                    'subject'=>$subject
                    
                 ]
             )->setFrom(
                 [
                    'email' => $owner_email,
                    'name' => $owner_name
                 ]
             )
            ->addTo(
                $client_email,
                $client_name
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
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Testimonial::testimonial');
    }
}
