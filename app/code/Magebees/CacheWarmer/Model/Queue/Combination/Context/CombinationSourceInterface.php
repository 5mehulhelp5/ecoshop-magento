<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue\Combination\Context;

use Magento\Framework\App\Http\Context;

interface CombinationSourceInterface
{
    public function getVariations(): array;

    public function getCombinationKey(): string;

    public function modifyRequest(array $combination, array &$requestParams, Context $context);

    public function prepareLog(array $crawlerLogData, array $combination): array;
}
