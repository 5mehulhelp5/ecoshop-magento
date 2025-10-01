<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Config\Source;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;
use Magento\Customer\Model\Group;
use Magento\Framework\Convert\DataObject;
use Magento\Persistent\Helper\Data;

class CustomerGroup extends \Magento\Customer\Model\Config\Source\Group
{
    public const PERSISTENT_PREFIX = 'persistent_';

    /**
     * @var Data
     */
    private $persistentData;

    public function __construct(
        GroupManagementInterface $groupManagement,
        DataObject $converter,
        Data $persistentData,
        ?GroupSourceLoggedInOnlyInterface $groupSourceForLoggedInCustomers = null
    ) {
        parent::__construct($groupManagement, $converter, $groupSourceForLoggedInCustomers);
        $this->persistentData = $persistentData;
    }

    public function toOptionArray()
    {
        $result = parent::toOptionArray();
        array_shift($result);

        if ($this->persistentData->isEnabled()) {
            foreach ($result as $customerGroupOption) {
                $result[] = [
                    'value' => self::PERSISTENT_PREFIX . $customerGroupOption['value'],
                    'label' => $customerGroupOption['label'] . __(' (Persistent)')
                ];
            }
        }

        array_unshift($result, [
            'value' => Group::NOT_LOGGED_IN_ID,
            'label' => __('NOT LOGGED IN')
        ]);

        return $result;
    }
}
