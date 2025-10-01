<?php
namespace Magebees\Onepagecheckout\Model\System\Config\Source;
use Magento\Framework\Option\ArrayInterface;

class StaticBlockPosition implements ArrayInterface
{
    const NOT_SHOW                     = 0;
    const SHOW_AT_TOP_CHECKOUT_PAGE    = 1;
    const SHOW_AT_BOTTOM_CHECKOUT_PAGE = 2;

    public function toOptionArray()
    {
        return [
            self::NOT_SHOW                     => __('None'),
            self::SHOW_AT_TOP_CHECKOUT_PAGE    => __('At Top of Checkout Page'),
            self::SHOW_AT_BOTTOM_CHECKOUT_PAGE => __('At Bottom of Checkout Page')
        ];
    }
}