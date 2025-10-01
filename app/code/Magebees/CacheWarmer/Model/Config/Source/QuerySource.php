<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class QuerySource implements OptionSourceInterface
{
    public const SOURCE_ALL_PAGES = 0;
    public const SOURCE_SITE_MAP = 1;
    public const SOURCE_TEXT_FILE = 2;
    public const SOURCE_SITE_MAP_AND_TEXT_FILE = 3;
    public const SOURCE_ACTIVITY = 4;
    public const SOURCE_COMBINE_TEXT_FILE_AND_PAGE_TYPES = 5;

    public function toOptionArray()
    {
        $options = [];

        $options[] = [
            'label' => __('Pages Types'),
            'value' => self::SOURCE_ALL_PAGES
        ];

        $options[] = [
            'label' => __('Text file with one link per line'),
            'value' => self::SOURCE_TEXT_FILE
        ];

        $options[] = [
            'label' => __('Sitemap XML'),
            'value' => self::SOURCE_SITE_MAP
        ];

        $options[] = [
            'label' => __('Sitemap XML and Text File together'),
            'value' => self::SOURCE_SITE_MAP_AND_TEXT_FILE
        ];

        return $options;
    }
}
