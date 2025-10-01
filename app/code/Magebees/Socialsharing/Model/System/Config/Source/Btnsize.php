<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Btnsize extends OptionArray
{
    const SMALL  = "16x16";
    const MEDIUM = "32x32";
    const LARGE  = "64x64";

    public function getOptionHash()
    {
        return [
            self::SMALL  => __('Style 1 (16x16)'),
            self::MEDIUM => __('Style 2 (32x32)'),
            self::LARGE  => __('Style 3 (64x64)'),
        ];
    }
}