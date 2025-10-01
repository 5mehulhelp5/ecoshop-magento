<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue\Combination\Context;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Crawler\RegistryConstants;
use Magebees\CacheWarmer\Model\Debug\ContextDebugService;
use GuzzleHttp\RequestOptions;
use Magento\Framework\App\Http\Context;

class DebugCombination implements CombinationSourceInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var ContextDebugService
     */
    private $contextDebugService;

    public function __construct(
        Config $configProvider,
        ContextDebugService $contextDebugService
    ) {
        $this->configProvider = $configProvider;
        $this->contextDebugService = $contextDebugService;
    }

    public function getVariations(): array
    {
        return [];
    }

    public function getCombinationKey(): string
    {
        return 'crawler_debug_context';
    }

    public function modifyRequest(array $combination, array &$requestParams, Context $context)
    {
        if ($this->configProvider->isDebugContext()) {
            $url = (string)$requestParams[RequestOptions::HEADERS][RegistryConstants::CRAWLER_URL_HEADER] ?? '';

            if ($url) {
                $vary = $context->getVaryString(); // Force run plugin chain over getVaryString method
                $contextDefaultData = $context->toArray()['default'];
                ksort($contextDefaultData);
                $debugData = [
                    'context' => ['vary' => $vary] + $context->getData(),
                    'defaults' => $contextDefaultData,
                ];
                $this->contextDebugService->addDebugLog($url, $debugData);
            }
        }
    }

    public function prepareLog(array $crawlerLogData, array $combination): array
    {
        return $crawlerLogData;
    }
}
