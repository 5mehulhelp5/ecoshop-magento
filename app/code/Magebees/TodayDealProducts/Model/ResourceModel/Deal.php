<?php
namespace Magebees\TodayDealProducts\Model\ResourceModel;

/**
 * Review resource model
 */
class Deal extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{	 protected $request;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->request = $request;
    }
    
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_today_deal', 'today_deal_id');
    }
    
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->request->getActionName()=='massStatus') {
            return $this;
        }
        
        //for solve posh theme import sample issue
        if ($this->request->getActionName()=='import') {
            return $this;
        }
        
        if ($object->getStores()) {
            $object->setStores(implode(",", $object->getStores()));
        }
        
        if (is_array($object->getCustomerGroupIds())) {
            $object->setCustomerGroupIds(implode(",", $object->getCustomerGroupIds()));
        }
                
        return $this;
    }
}
