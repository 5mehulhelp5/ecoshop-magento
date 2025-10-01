<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magebees\CacheWarmer\Model\ResourceModel\Reports\Collection;

class Log extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_log';

    /**
     * @var Reports\Collection
     */
    private $reportsCollection;

    public function __construct(
        \Magebees\CacheWarmer\Model\ResourceModel\Reports\Collection $reportsCollection,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->reportsCollection = $reportsCollection;
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    public function deleteWithLimit($limit)
    {
        if ($limit <= 0) {
            return;
        }

        $limit = (int)$limit;

        // phpcs:ignore Magento2.SQL.RawQuery.FoundRawSql
        $query = "DELETE FROM `{$this->getMainTable()}` LIMIT $limit";

        $this->getConnection()->query($query);
    }

    public function flush()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function getStatsByStatus($code = Collection::DATE_TYPE_DAY)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['status', 'COUNT(id)'])
            ->group('status');
        $this->reportsCollection->addWhereCondition($code, $select, 'created_at');

        return $this->getConnection()->fetchPairs($select);
    }

    public function getStatsByDay()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['period' => 'DATE(created_at)', 'count' => 'COUNT(id)'])
            ->order('period')
            ->group('DATE(created_at)');

        return $this->getConnection()->fetchAll($select);
    }
}
