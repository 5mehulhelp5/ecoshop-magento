<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\Provider;

interface SourceProviderInterface
{
    public function getPagesBySourceType(int $sourceType, int $pagesLimit): array;
}
