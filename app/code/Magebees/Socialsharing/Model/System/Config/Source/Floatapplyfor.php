<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;

class Floatapplyfor extends OptionArray
{
    const ALL_PAGES    = "all_pages";
    const SELECT_PAGES = "select_pages";
	//const BY_LINKS = "by_links";

    public function getOptionHash()
    {
        return [
            self::ALL_PAGES    => __('All Pages'),
            self::SELECT_PAGES => __('Select Pages')
        ];
    }
}
