<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\Provider;

use Magebees\CacheWarmer\Model\Source;
use Psr\Log\LoggerInterface;

class SimpleSource implements SourceProviderInterface
{
    /**
     * @var Source\Factory
     */
    private $sourceFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Source\Factory $sourceFactory,
        LoggerInterface $logger
    ) {
        $this->sourceFactory = $sourceFactory;
        $this->logger = $logger;
    }

    public function getPagesBySourceType(int $sourceType, int $pagesLimit): array
    {
        $pages = [];

        try {
            $source = $this->sourceFactory->create($sourceType);
            $pages = $source->getPages($pagesLimit, '');

            if (count($pages) > $pagesLimit) {
                $pages = array_slice($pages, 0, $pagesLimit);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return $pages;
    }
}
