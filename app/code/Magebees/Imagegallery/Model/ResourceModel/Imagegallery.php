<?php
namespace Magebees\Imagegallery\Model\ResourceModel;

/**
 * Review resource model
 */
class Imagegallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_imagegallery', 'image_id');
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
