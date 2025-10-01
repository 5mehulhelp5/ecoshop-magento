<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler\HttpClient;

use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\Collection as PageCollection;

interface CrawlerClientInterface
{
    public function setMethod(string $method);

    public function execute(
        PageCollection $pageCollection,
        array $requestCombinations,
        \Closure $getRequestParams
    ): \Generator;
}
