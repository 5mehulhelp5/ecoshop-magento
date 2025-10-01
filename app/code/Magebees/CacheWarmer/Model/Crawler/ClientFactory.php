<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Crawler\HttpClient;
use Magento\Framework\ObjectManagerInterface;

class ClientFactory
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        Config $configProvider,
        ObjectManagerInterface $objectManager
    ) {
        $this->configProvider = $configProvider;
        $this->objectManager = $objectManager;
    }

    public function create(): HttpClient\CrawlerClientInterface
    {
        if ($this->configProvider->isMultipleCurl() && $this->configProvider->getProcessesNumber() > 1) {
            return $this->objectManager->create(HttpClient\AsyncClient::class);
        }

        return $this->objectManager->create(HttpClient\Client::class);
    }
}
