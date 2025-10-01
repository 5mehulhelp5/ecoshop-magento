<?php
namespace Magebees\Blog\Controller\Comment;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magebees\Blog\Model\Provider;
use ReCaptcha\ReCaptcha;
class Index extends \Magento\Framework\App\Action\Action
{
	protected $request;
	protected $_collection;
	protected $_scopeConfig;
	protected $comment;
	protected $_provider;
	protected $_timezoneInterface;
	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Framework\App\Request\Http $request,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Comment $comment,	
	\Magento\Framework\Module\Dir\Reader $reader,
	Provider $provider,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	array $data = [])
	{
		$this->request = $request;
		$this->_collection = $collection;
		$this->_scopeConfig = $_scopeConfig;
		$this->comment = $comment;
		$this->_provider = $provider;
		$this->_timezoneInterface = $timezoneInterface;
		parent::__construct($context);
	}
	 public function execute()
    {
		$data = $this->request->getParams();
		
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
       	$data=$this->getRequest()->getPost()->toarray();
		
		if($data)
		{
			$redirecturl = $data['post_url'];
			
			if(!isset($data['parent_id']))
			{
				$data['parent_id'] = 0;
			}
			$data['creation_time'] = $currentDate;
			try {
				$captch_settings = 
					$this->_scopeConfig->getValue('blog/post_view/comment/recaptcha',\Magento\Store\Model\ScopeInterface::SCOPE_STORE); 
				$captchaenable = false;
				
				if($captch_settings['enabled'])
				{
					$captchaenable = true;
				}
				
				if($captchaenable==1){
					$captcha_validation = false;
				if($captch_settings['captchatype']=='invisible')
				{
				$secret_key = $captch_settings['invisible_secret_key'];
				}elseif($captch_settings['captchatype']=='visible')
				{
				$secret_key = $captch_settings['visible_secret_key'];
				}
				
				if($captch_settings['captchatype']=='invisible')
				{
				$site_key = $captch_settings['invisible_site_key'];
				$secret_key = $captch_settings['invisible_secret_key'];
				$recaptcha = $this->getRequest()->getPostValue('captcha-response-comment');
				if(isset($data['captcha-response-comment-reply']) &&(!empty($data['captcha-response-comment-reply'])))
				{
				$recaptcha = $this->getRequest()->getPostValue('captcha-response-comment-reply');
				}else 		if(isset($data['captcha-response-comment']) &&(!empty($data['captcha-response-comment'])))
				{
				$recaptcha = $this->getRequest()->getPostValue('captcha-response-comment');
				}
					
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$recaptcha);
        		$responseData = json_decode($verifyResponse);
        		if($responseData->success){
					$captcha_validation = true;
				}else{
					$captcha_validation = false;
				}
			
				}else if($captch_settings['captchatype']=='visible')
				{
					$site_key = $captch_settings['visible_site_key'];
					$secret_key = $captch_settings['visible_secret_key'];
					$recaptcha = $this->getRequest()->getPostValue('g-recaptcha-response');
					if (!empty($recaptcha) && 
					$this->_provider->validate($recaptcha, $secret_key)) {
						$captcha_validation = true;
					}else{
						$captcha_validation = false;
					}
				}
					if($captcha_validation)
					{
					$this->comment->setData($data);
					$this->comment->setCreatedTime($currentDate)->setUpdateTime($currentDate);
					$this->comment->save();
					$this->messageManager->addSuccess(__('You submitted your comment for moderation.'));
						
					}else{
						$this->messageManager->addErrorMessage(
                	__('Please Valid Captcha.'));
					}
					
					
				}else{
				
					$this->comment->setData($data);
					$this->comment->setCreatedTime($currentDate)->setUpdateTime($currentDate);
					$this->comment->save();
					$this->messageManager->addSuccess(__('You submitted your comment for moderation.'));
					
				}
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($redirecturl);
				return $resultRedirect;
			}
				catch (\Magento\Framework\Model\Exception $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	}
				catch (\RuntimeException $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	} 
				catch (\Exception $e)
				{
					$this->messageManager->addError($e->getMessage());
                	
            	}
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($redirecturl);
				return $resultRedirect;
				
		}
		$this->_redirect('*/*/');
		 
	}
		

}
