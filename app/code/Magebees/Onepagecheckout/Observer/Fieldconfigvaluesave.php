<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class Fieldconfigvaluesave implements ObserverInterface
{
	protected $scopeConfig;
	protected $configFactory;
	protected $_storeManager;
	protected $_request;
	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory,
		\Magento\Framework\App\RequestInterface $request
		) {
		$this->scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->configFactory = $configFactory;
		$this->_request = $request;
	}
	 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$postData = $this->_request->getPost();
    	if ($observer->getEvent()->getStore()) {
            $scope = 'stores';
            $scopeId = $observer->getEvent()->getStore();
        } elseif ($observer->getEvent()->getWebsite()) {
            $scope = 'websites';
            $scopeId = $observer->getEvent()->getWebsite();
        } else {
            $scope = 'default';
            $scopeId = 0;
        }
        if(!empty($postData['groups']['field_position_management'])){
			for($i=0;$i < 20;$i++){
				if(isset($postData['groups']['field_position_management']['fields']['row_'.$i]['value']))
				{
					$this->configFactory->saveConfig(
					'magebees_Onepagecheckout/field_position_management/row_'.$i,
					$postData['groups']['field_position_management']['fields']['row_'.$i]['value'],
					$scope,
					$scopeId
					);
				}
			}
		}
		return;
	}
}