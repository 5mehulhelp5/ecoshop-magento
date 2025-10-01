<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\ObserverInterface;
class Showhideheaderfooter implements ObserverInterface
{
	protected $_configHelper;
	protected $pageConfig;
	
    public function __construct(
    \Magento\Framework\View\Page\Config $pageConfig,
	\Magebees\Onepagecheckout\Helper\Configurations $configHelper
    ) {    
        $this->pageConfig = $pageConfig;
		$this->_configHelper = $configHelper;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$event = $observer->getEvent();
		$action = $event->getFullActionName();
		
		$showHeaderFooter = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/show_header_footer');
		
		if ($action == 'checkout_index_index' && $showHeaderFooter == 1) {
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			$layoutUpdate->addHandle('show_header_footer');
		}
	}	
}