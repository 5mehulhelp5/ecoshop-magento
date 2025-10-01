<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\Provider;

class CompositeSource implements SourceProviderInterface
{
    /**
     * @var SimpleSource
     */
    private $simpleSourceProvider;

    /**
     * @var array
     */
    private $sourceTypeIds;

    public function __construct(
        SimpleSource $simpleSourceProvider,
        array $sourceTypeIds = []
    ) {
        $this->simpleSourceProvider = $simpleSourceProvider;
        $this->sourceTypeIds = $sourceTypeIds;
    }

    public function getPagesBySourceType(int $sourceType, int $pagesLimit): array
    {
        $pages = [];

        foreach ($this->sourceTypeIds as $sourceTypeId) {
            $pagesBatch = $this->simpleSourceProvider->getPagesBySourceType((int)$sourceTypeId, $pagesLimit);
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $pages = array_merge($pages, $pagesBatch);
            $pagesLimit -= count($pagesBatch);

            if ($pagesLimit <= 0) {
                break;
            }
        }

        return $pages;
    }
}
