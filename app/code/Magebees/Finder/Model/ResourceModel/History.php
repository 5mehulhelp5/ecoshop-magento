<?php
namespace Magebees\Finder\Model\ResourceModel;

/**
 * Review resource model
 */
class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_import_history', 'history_id');
    }
    
}
