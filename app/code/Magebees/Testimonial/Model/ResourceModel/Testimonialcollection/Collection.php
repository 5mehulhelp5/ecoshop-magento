<?php

namespace Magebees\Testimonial\Model\ResourceModel\Testimonialcollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Testimonial\Model\Testimonialcollection', 'Magebees\Testimonial\Model\ResourceModel\Testimonialcollection');
    }
}
