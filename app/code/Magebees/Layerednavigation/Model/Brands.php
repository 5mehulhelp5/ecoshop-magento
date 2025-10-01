<?php
namespace Magebees\Layerednavigation\Model;

class Brands extends \Magento\Framework\Model\AbstractModel
{
    
    protected function _construct()
    {
        $this->_init('Magebees\Layerednavigation\Model\ResourceModel\Brands');
    }
}
