<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DebugContext extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_context_debug';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    public function flush()
    {
        $this->getConnection()->delete(
            $this->getMainTable()
        );
    }
}
