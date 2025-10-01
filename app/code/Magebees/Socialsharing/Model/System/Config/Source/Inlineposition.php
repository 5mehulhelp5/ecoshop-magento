<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Inlineposition extends OptionArray
{
    const TOP_CONTENT    = "top_content";
    const BOTTOM_CONTENT = "bottom_content";

    public function getOptionHash()
    {
        return [
            self::TOP_CONTENT    => __('Top Content'),
            self::BOTTOM_CONTENT => __('Bottom Content'),
        ];
    }
}