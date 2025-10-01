<?php
namespace Magebees\Finder\Model\ResourceModel;

/**
 * Products resource model
 */
class Ymmvalue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_finder_ymm_value', 'ymm_value_id');
    }
}
