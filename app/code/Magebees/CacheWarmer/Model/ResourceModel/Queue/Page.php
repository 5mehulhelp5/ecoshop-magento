<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Page extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_queue_page';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function getMaxRate()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [new \Zend_Db_Expr('MAX(rate)')]);

        return $this->getConnection()->fetchOne($select);
    }

    public function getPageByUrl(string $url, ?int $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('url = ?', $url);

        if ($storeId) {
            $select->where('store = ?', $storeId);
        }

        $select->where('activity_id is NULL');

        return $connection->fetchOne($select);
    }
}
