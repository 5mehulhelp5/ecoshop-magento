<?php 
namespace Magebees\Navigationmenu\Observer;
class Logout implements \Magento\Framework\Event\ObserverInterface 
{
	protected $cacheManager;
	public function __construct(
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$cache_type = array();
		$cache_type[] = 'block_html';
		$this->cacheManager->clean($cache_type);
	}
}