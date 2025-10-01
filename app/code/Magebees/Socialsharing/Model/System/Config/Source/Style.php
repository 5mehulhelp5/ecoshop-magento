<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Style extends OptionArray
{
    const HORIZONTAL = "horizontal";
    const VERTICAL   = "vertical";

    public function getOptionHash()
    {
        return [
            self::HORIZONTAL => __('Horizontal'),
            self::VERTICAL   => __('Vertical'),
        ];
    }
}