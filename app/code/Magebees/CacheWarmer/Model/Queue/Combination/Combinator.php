<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue\Combination;

use Magebees\CacheWarmer\Model\Queue\Combination\Context\CombinationSourceInterface;
use Magebees\CacheWarmer\Model\Queue\Combination\Context\CurrencyCombination;
use Magebees\CacheWarmer\Model\Queue\Combination\Context\StoreCombination;
use Magento\Store\Model\StoreManagerInterface;

class Combinator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param array $combination
     * @param CombinationSourceInterface $combinationSource
     * @return array
     */
    public function execute(array $combination, $combinationSource)
    {
        $result = [];
        $variations = $combinationSource->getVariations();
        $combinationKey = $combinationSource->getCombinationKey();

        if (!$variations) {
            return $combination;
        }

        $availableCurrencyCodes = [];
        $baseStoreCurrency = false;

        foreach ($combination as $combinationUnit) {
            if ($combinationKey === CurrencyCombination::COMBINATION_KEY) {
                $store = false;

                if (isset($combinationUnit[StoreCombination::COMBINATION_KEY])) {
                    $store = $this->storeManager->getStore($combinationUnit[StoreCombination::COMBINATION_KEY]);
                } elseif (!$baseStoreCurrency) {
                    $store = $this->storeManager->getWebsite()->getDefaultStore();
                }

                if ($store) {
                    $baseStoreCurrency = $store->getDefaultCurrency()->getCode();
                    $availableCurrencyCodes = $store->getAvailableCurrencyCodes(true);
                }
            }

            foreach ($variations as $variation) {
                if (!$availableCurrencyCodes
                    || in_array($variation, $availableCurrencyCodes)
                ) {
                    $combinationUnit[$combinationKey] = !$baseStoreCurrency || ($variation !== $baseStoreCurrency)
                        ? $variation
                        : null;
                    $result[] = $combinationUnit;
                }
            }
        }

        return $result;
    }
}
