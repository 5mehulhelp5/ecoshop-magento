<?php
namespace Magebees\Finder\Model\ResourceModel;

/**
 * Products resource model
 */
class Mapvalue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_finder_map_value', 'map_value_id');
    }
}
