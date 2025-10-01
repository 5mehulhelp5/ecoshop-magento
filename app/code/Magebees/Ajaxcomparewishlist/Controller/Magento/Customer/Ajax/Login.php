<?php
namespace Magebees\Ajaxcomparewishlist\Controller\Magento\Customer\Ajax;
class Login extends \Magento\Customer\Controller\Ajax\Login
{
    public function execute()
    {	
		$data = $this->helper->jsonDecode($this->getRequest()->getContent());
		if($data){
				$sessVal = "";
				if(isset($data['mbwishcomparelogin'])){
					if($data['mbwishcomparelogin']!= "" && $data['mbwishcomparelogin'] == 1){
						$cacheManager = $this->_objectManager->create('\Magento\Framework\App\Cache\Manager');
						$cache_type = array();
						$cache_type[] = 'block_html';
						$cacheManager->clean($cache_type);
						$mbSession = $this->_objectManager->create('Magento\Framework\Session\SessionManagerInterface');
						$mbSession->start();
						$sessVal = $mbSession->getChkLogin();
						if($sessVal != ""){
							$mbSession->unsChkLogin();
						}
						$mbSession->setChkLogin(rand());
					}
				}
			
		}
		return parent::execute();
	}
}