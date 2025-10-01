<?php
namespace Magebees\PocoBase\Observer;

use Magento\Framework\Event\ObserverInterface;
class CacheCleanObserver implements ObserverInterface
{	protected $helper;
	public function __construct(
        \Magebees\PocoBase\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

	public function execute(\Magento\Framework\Event\Observer $observer){
		$this->helper->cachePrograme();
	}
}
