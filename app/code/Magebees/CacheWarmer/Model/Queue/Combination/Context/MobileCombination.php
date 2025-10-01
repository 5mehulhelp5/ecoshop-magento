<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue\Combination\Context;

use Magebees\CacheWarmer\Model\Config;
use GuzzleHttp\RequestOptions;
use Magento\Framework\App\Http\Context;

class MobileCombination implements CombinationSourceInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(Config $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function getVariations(): array
    {
        return $this->configProvider->isProcessMobile() ? [true, false] : [];
    }

    public function getCombinationKey(): string
    {
        return 'crawler_mobile';
    }

    public function modifyRequest(array $combination, array &$requestParams, Context $context)
    {
        if ($isMobile = $combination[$this->getCombinationKey()] ?? null) {
            $requestParams[RequestOptions::HEADERS]['User-Agent'] = $this->configProvider->getMobileAgent();
        }
    }

    public function prepareLog(array $crawlerLogData, array $combination): array
    {
        if ($isMobile = $combination[$this->getCombinationKey()] ?? null) {
            $crawlerLogData['mobile'] = $isMobile;
        }

        return $crawlerLogData;
    }
}
