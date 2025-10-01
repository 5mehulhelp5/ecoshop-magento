<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Simplexml;

use Magento\Framework\Simplexml\Config as SimplexmlConfig;

class Config extends SimplexmlConfig
{
    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        $text = str_replace('&amp;', '&', $text);
        $text = str_replace('&', '&amp;', $text);

        return $text;
    }
}
