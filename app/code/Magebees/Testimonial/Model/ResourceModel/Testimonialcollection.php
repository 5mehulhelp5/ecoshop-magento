<?php
namespace Magebees\Testimonial\Model\ResourceModel;

class Testimonialcollection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_customer_testimonials', 'testimonial_id');
    }
}
