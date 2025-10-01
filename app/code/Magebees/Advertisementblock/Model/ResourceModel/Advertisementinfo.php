<?php
namespace Magebees\Advertisementblock\Model\ResourceModel;

class Advertisementinfo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_advertisement_information', 'advertisement_id');
    }
}
