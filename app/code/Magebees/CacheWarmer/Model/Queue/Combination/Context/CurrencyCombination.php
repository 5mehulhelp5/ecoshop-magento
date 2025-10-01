<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue\Combination\Context;

use Magebees\CacheWarmer\Helper\Http as HttpHelper;
use Magebees\CacheWarmer\Model\Config;
use GuzzleHttp\RequestOptions;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\StoreManagerInterface;

class CurrencyCombination implements CombinationSourceInterface
{
    public const COMBINATION_KEY = 'crawler_currency';

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var string
     */
    private $defaultCurrency;

    public function __construct(
        Config $configProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->configProvider = $configProvider;
        $this->defaultCurrency = $storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode();
    }

    public function getVariations(): array
    {
        return $this->configProvider->getCurrencies();
    }

    public function getCombinationKey(): string
    {
        return CurrencyCombination::COMBINATION_KEY;
    }

    public function modifyRequest(array $combination, array &$requestParams, Context $context)
    {
        if ($currency = $combination[$this->getCombinationKey()] ?? null) {
            $requestParams[RequestOptions::HEADERS][HttpHelper::CURRENCY_HEADER] = $currency;
            $context->setValue(
                Context::CONTEXT_CURRENCY,
                $currency,
                $this->defaultCurrency
            );
        }
    }

    public function prepareLog(array $crawlerLogData, array $combination): array
    {
        if ($currency = $combination[$this->getCombinationKey()] ?? null) {
            $crawlerLogData['currency'] = $currency;
        }

        return $crawlerLogData;
    }
}
