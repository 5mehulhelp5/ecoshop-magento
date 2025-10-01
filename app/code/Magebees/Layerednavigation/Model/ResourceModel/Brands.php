<?php
namespace Magebees\Layerednavigation\Model\ResourceModel;

class Brands extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_layernav_brand', 'brand_id');
    }
}
