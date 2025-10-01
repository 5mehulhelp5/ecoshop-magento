<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
class OnestepcheckoutObserver implements ObserverInterface
{
	protected $scopeConfig;
	protected $_storeManager;
	protected $_request;
	protected $_configFactory;
	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory
		) {
		$this->scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_request = $request;
  		$this->_configFactory = $configFactory;
	}
	 
    public function execute(\Magento\Framework\Event\Observer $observer)
	{
		return true;
	}
}