<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Floatposition extends OptionArray
{
    const LEFT  = "left";
    const RIGHT = "right";

    public function getOptionHash()
    {
        return [
            self::LEFT  => __('Left'),
            self::RIGHT => __('Right'),
        ];
    }
}
