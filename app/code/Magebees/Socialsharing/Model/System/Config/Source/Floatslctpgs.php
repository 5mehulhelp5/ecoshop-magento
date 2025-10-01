<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Floatslctpgs extends OptionArray
{
    const CATEGORY_PAGE = "category_page";
    const PRODUCT_PAGE  = "product_page";
    const CONTACT_US    = "contact_us";

    public function getOptionHash()
    {
        return [
            self::PRODUCT_PAGE  => __('Products Page'),
            self::CATEGORY_PAGE => __('Categories Page'),
            self::CONTACT_US    => __('Contact Us')
        ];
    }
}
