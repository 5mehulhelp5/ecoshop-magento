<?php
namespace Magebees\Productlisting\Model\ResourceModel;

/**
 * Review resource model
 */
class SelectProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_productlisting_select_products', 'select_product_id');
    }
}
