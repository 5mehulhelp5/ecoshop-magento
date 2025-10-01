<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magebees\CacheWarmer\Api\Data\FlushesLogInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FlushesLog extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_flushes_log';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, FlushesLogInterface::LOG_ID);
    }

    public function truncateTable()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function clearTable(string $date): void
    {
        $this->getConnection()->delete($this->getMainTable(), [FlushesLogInterface::DATE . ' < ?' => $date]);
    }
}
