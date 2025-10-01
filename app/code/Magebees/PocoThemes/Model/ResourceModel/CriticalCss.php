<?php
namespace Magebees\PocoThemes\Model\ResourceModel;

/**
 * Review resource model
 */
class CriticalCss extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_critical_css', 'id');
    }
	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
        ) {
        parent::__construct($context);
        
    }
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_afterSave($object);
    }
    
}
