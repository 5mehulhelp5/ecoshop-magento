<?php
namespace Magebees\Advertisementblock\Model\ResourceModel;

class Advertisementimages extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_advertisement_images', 'image_id');
    }
}
