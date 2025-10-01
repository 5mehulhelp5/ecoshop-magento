<?php

namespace Magebees\TodayDealProducts\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $_customerSession;
    protected $_timezoneInterface;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_storeManager = $storeManager;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getCustomerGroupId()
    {
        return $this->_customerSession->getCustomerGroupId();
    }
    
     /**
      * Check whether it is single store mode
      *
      * @return bool
      */
    public function isSingleStoreMode()
    {
        return (bool)$this->_storeManager->isSingleStoreMode();
    }
    
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore(true)->getId();
    }

    public function getCurrentDate()
    {
        $today = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        return $today;
    }
}
