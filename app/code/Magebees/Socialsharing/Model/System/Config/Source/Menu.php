<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Menu extends OptionArray
{
    const ON_HOVER = "hover";
    const ON_CLICK = "click";

    public function getOptionHash()
    {
        return [
            self::ON_HOVER => __('Hover'),
            self::ON_CLICK => __('Click'),
        ];
    }
}
