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
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Http\Context;
use Magento\Persistent\Helper\Data;

class CustomerGroupCombination implements CombinationSourceInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Data
     */
    private $persistentData;

    public function __construct(
        Config $configProvider,
        Data $persistentData
    ) {
        $this->configProvider = $configProvider;
        $this->persistentData = $persistentData;
    }

    public function getVariations(): array
    {
        $groups = array_filter($this->configProvider->getCustomerGroups(), function ($customerGroup) {
            if (!$this->persistentData->isEnabled() && $this->isGroupPersistent($customerGroup)) {
                return false;
            }

            return true;
        });

        if (empty($groups)) {
            $groups[] = Group::NOT_LOGGED_IN_ID;
        }

        return $groups;
    }

    public function getCombinationKey(): string
    {
        return 'crawler_customer_group';
    }

    public function modifyRequest(array $combination, array &$requestParams, Context $context)
    {
        if ($customerGroup = $combination[$this->getCombinationKey()] ?? null) {
            $this->processPersistentCustomerGroup($customerGroup, $context);
            $requestParams[RequestOptions::HEADERS][HttpHelper::CUSTOMER_GROUP_HEADER] = $customerGroup;
            $context->setValue(
                CustomerContext::CONTEXT_GROUP,
                $customerGroup,
                Group::NOT_LOGGED_IN_ID
            );
            $context->setValue(
                CustomerContext::CONTEXT_AUTH,
                (bool)$customerGroup,
                false
            );
        }
    }

    public function prepareLog(array $crawlerLogData, array $combination): array
    {
        if ($customerGroup = $combination[$this->getCombinationKey()] ?? null) {
            $crawlerLogData['customer_group'] = $customerGroup;
            $crawlerLogData['persistent_customer_group'] = 0;

            if ($this->isGroupPersistent($customerGroup)) {
                $crawlerLogData['customer_group'] = str_replace(
                    Config\Source\CustomerGroup::PERSISTENT_PREFIX,
                    '',
                    $customerGroup
                );
                $crawlerLogData['persistent_customer_group'] = 1;
            }
        }

        return $crawlerLogData;
    }

    private function isGroupPersistent($customerGroup): bool
    {
        return strpos($customerGroup, Config\Source\CustomerGroup::PERSISTENT_PREFIX) === 0;
    }

    /**
     * Persistent context parameter can be set only for logged in customers
     * @see \Magento\Persistent\Model\Plugin\PersistentCustomerContext::beforeGetVaryString
     *
     * @param string $customerGroup
     * @param Context $context
     * @return string
     */
    private function processPersistentCustomerGroup(string &$customerGroup, Context $context)
    {
        if ($this->isGroupPersistent($customerGroup)) {
            $customerGroup = str_replace(Config\Source\CustomerGroup::PERSISTENT_PREFIX, '', $customerGroup);
            $context->setValue('PERSISTENT', 1, 0);
        }

        return $customerGroup;
    }
}
