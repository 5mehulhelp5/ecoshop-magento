<?php
namespace Magebees\TodayDealProducts\Block;

use Magento\Framework\View\Element\Template;

class DealTimerListingProducts extends Template {
    public $_dealProductIds = array();
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\TodayDealProducts\Model\DealFactory $dealFactory,
		\Magebees\TodayDealProducts\Helper\Data $dealHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_dealFactory = $dealFactory;
        $this->_dealHelper = $dealHelper;
		$this->_objectManager = $objectManager;
        parent::__construct($context);
    }

	public function getDealProductsCollection(){
		
		$storeId = $this->_dealHelper->getCurrentStoreId();
		$groupId = $this->_dealHelper->getCustomerGroupId();
		if($this->getUniqueCode()){
			$today_deal_id = $this->_dealFactory->create()->load($this->getUniqueCode(),'unique_code')->getId();
		}elseif($this->getTodayDealId()){
			$today_deal_id = $this->getTodayDealId();
		}else{
			$today_deal_id = 0;
		}
		
		$now = $this->_dealHelper->getCurrentDate();
		$this->_dealData = $this->_dealFactory->create()->getCollection()
							//->addFieldToFilter('today_deal_id',$today_deal_id)
							->addFieldToFilter('is_active',1)
							->setOrder('sort_order', 'ASC');
		
		if($today_deal_id){
			$this->_dealData->addFieldToFilter('today_deal_id',$today_deal_id);
		}
		
		if (!$this->_dealHelper->isSingleStoreMode()) {
			$this->_dealData->addFieldToFilter('stores', array(array('finset' => $storeId)));
		}
		
		if($this->_dealData->getSize()){
			$cond = null;
            $cond = $this->_dealData->getFirstItem()->getData('cond_serialize');
            
            $ruleModel = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Rule');
            $ruleModel->setConditions([]);
            $ruleModel->setConditionsSerialized($cond);
            $product_ids = [];
            $product_ids = $ruleModel->getMatchingProductIds();
            $this->_dealProductIds = array_keys($product_ids);
			$layoutoptions = null;
			$layoutoptions = $this->_dealData->getFirstItem()->getData('layoutoptions');
			$layoutoptions_array = array();
			$layoutoptions_array = json_decode($layoutoptions,true);
			$this->_dealData->getFirstItem()->setData('layoutoptions',$layoutoptions_array);
			$this->setDealData($this->_dealData->getFirstItem()->getData());
		}
		$this->_count_timer['to_time'] = strtotime((string)$this->getDealData('to_date'));
		$this->_count_timer['from_time'] = strtotime((string)$this->getDealData('from_date'));
		$this->_count_timer['current_time'] =  strtotime((string)$this->_dealHelper->getCurrentDate());
		$this->_count_timer['time_format'] = $this->getDealData('timer_format')?1:2;
		$this->setCountTimer($this->_count_timer);
	}
    
   
    

}