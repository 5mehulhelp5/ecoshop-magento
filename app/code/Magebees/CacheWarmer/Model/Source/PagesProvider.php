<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Source\Provider;
use Psr\Log\LoggerInterface;

class PagesProvider
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Provider\SimpleSourceFactory
     */
    private $simpleSourceFactory;

    /**
     * @var Provider\SourceProviderInterface[]
     */
    private $sourceTypeProviders;

    public function __construct(
        Config $configProvider,
        LoggerInterface $logger,
        Provider\SimpleSourceFactory $simpleSourceFactory,
        array $sourceTypeProviders = []
    ) {
        foreach ($sourceTypeProviders as $provider) {
            if (!($provider instanceof Provider\SourceProviderInterface)) {
                throw new \LogicException(
                    sprintf('Source provider must implement %s', Provider\SourceProviderInterface::class)
                );
            }
        }

        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->simpleSourceFactory = $simpleSourceFactory;
        $this->sourceTypeProviders = $sourceTypeProviders;
    }

    public function getSourcePages(int $sourceType, int $pagesLimit = 500): array
    {
        if (isset($this->sourceTypeProviders[$sourceType])) {
            $provider = $this->sourceTypeProviders[$sourceType];
        } else {
            /**
             * Fall back to simple source if composite source is not defined for requested source type
             */
            $provider = $this->simpleSourceFactory->create();
        }

        $urlIgnoreList = $this->configProvider->getExcludePages();
        $pages = $provider->getPagesBySourceType($sourceType, $pagesLimit);

        foreach ($pages as $pageKey => $pageData) {
            if ($this->isUrlIgnored($pageData['url'] ?? '', $urlIgnoreList)) {
                unset($pages[$pageKey]);
            }
        }

        usort($pages, function ($first, $second) {
            return ($first['rate'] ?? 0) <=> ($second['rate'] ?? 0);
        });

        return $pages;
    }

    private function isUrlIgnored(string $url, array $ignoreList)
    {
        foreach ($ignoreList as $pattern) {
            if (preg_match("|{$pattern['expression']}|", $url)) {
                return true;
            }
        }

        return false;
    }
}
