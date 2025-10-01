<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Inlineapplyfor extends OptionArray
{
    const HOME_PAGE     = "home_page";
    const CATEGORY_PAGE = "category_page";
    const PRODUCT_PAGE  = "product_page";

    public function getOptionHash()
    {
        return [
            self::HOME_PAGE     => __('Home Page'),
            self::CATEGORY_PAGE => __('Categories Page'),
            self::PRODUCT_PAGE  => __('Products Page'),
        ];
    }
}
