<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Model\ResourceModel;

/**
 * Review resource model
 */
class Productlabel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $request;   
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
        $this->_init('magebees_productlabel', 'label_id');
    }
    
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->request->getActionName()=='massStatus') {
            return $this;
        }
        
        //for solve poco theme import sample issue
        if($this->request->getActionName()=='import'){
            return $this;
        }
        
        if ($object->getStores()) {
            $object->setStores(implode(",", (array)$object->getStores()));
        }
                
        if (is_array($object->getCustomerGroupIds())) {
            $object->setCustomerGroupIds(implode(",", (array)$object->getCustomerGroupIds()));
        }
        
        return $this;
    }
 
}
