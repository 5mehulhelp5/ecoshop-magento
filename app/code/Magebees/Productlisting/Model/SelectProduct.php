<?php
namespace Magebees\Productlisting\Model;

class SelectProduct extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Productlisting\Model\ResourceModel\SelectProduct');
    }
}
