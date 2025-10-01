<?php
namespace Magebees\Layerednavigation\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ChangePageLayoutObserver implements ObserverInterface
{	
	protected $pageConfig;
	protected $scopeConfig;
		
    public function __construct(
		\Magento\Framework\View\Page\Config $pageConfig,
		ScopeConfigInterface $scopeConfig
    ) {    
        $this->pageConfig = $pageConfig;
		$this->scopeConfig = $scopeConfig;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer){
		$event = $observer->getEvent();
		$action = $event->getFullActionName();
		$enableAjax = $this->scopeConfig->getValue('layerednavigation/setting/ajaxenable',ScopeInterface::SCOPE_STORE);
		
		if ($action == 'catalog_category_view'){
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			if($enableAjax){
				$layoutUpdate->addHandle('catalog_category_view_type_layered_ajax');
			}else{
				$layoutUpdate->addHandle('catalog_category_view_type_layered');				
			}
		}
	}
}