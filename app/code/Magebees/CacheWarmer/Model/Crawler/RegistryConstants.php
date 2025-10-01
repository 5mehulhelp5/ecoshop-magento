<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler;

class RegistryConstants
{
    public const CRAWLER_AGENT_EXTENSION = 'Magebees_CacheWarmer';
    public const CRAWLER_SESSION_COOKIE_NAME = 'PHPSESSID';
    public const CRAWLER_SESSION_COOKIE_VALUE = 'magebees-cachewarmer-crawler';
    public const CRAWLER_URL_HEADER = 'X-Magebees-Crawler-Url';
    public const CRAWLER_USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) '
        . 'Chrome/58.0.3029.110 Safari/537.36';
}
