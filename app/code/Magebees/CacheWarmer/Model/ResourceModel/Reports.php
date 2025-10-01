<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magebees\CacheWarmer\Model\Reports as ReportsModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Reports extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_reports';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, ReportsModel::REPORT_ID);
    }

    public function clearTable(string $date): void
    {
        $this->getConnection()->delete($this->getMainTable(), ['date < ?' => $date]);
    }
}
