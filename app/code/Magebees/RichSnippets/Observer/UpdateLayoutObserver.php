<?php
namespace Magebees\RichSnippets\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateLayoutObserver implements ObserverInterface
{      
   
    protected $scopeConfig;  
  
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }    
  
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
        $layout = $observer->getData('layout');
		/** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }     
          $enabled= $this->scopeConfig->getValue('richsnippets/setting/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
           if($enabled)
           {  
                $layout->getUpdate()->addHandle('richsnippet_remove_schema');
           }
       

        return $this;
    }
}
