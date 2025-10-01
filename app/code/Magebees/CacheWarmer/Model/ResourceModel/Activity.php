<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Activity extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_activity';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    /**
     * Get activity by url and version (mobile or not)
     */
    public function matchUrl(
        string $url,
        bool $mobile,
        ?int $store = null,
        ?string $currency = null,
        ?int $customerGroupId = null
    ): bool {
        $select = $this->getConnection()->select()
            ->from(['activity' => $this->getMainTable()], 'activity.id')
            ->where('activity.url = ?', $url)
            ->where('activity.mobile = ?', $mobile)
            ->where('activity.store = ?', $store)
            ->where('activity.currency = ?', $currency)
            ->where('activity.customer_group = ?', $customerGroupId);

        return (int)$this->getConnection()->fetchOne($select);
    }
}
