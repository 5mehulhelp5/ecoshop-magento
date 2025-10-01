<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel;

use Magebees\CacheWarmer\Model\FlushPages as FlushPagesModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FlushPages extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_pages_to_flush';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, FlushPagesModel::ID);
    }
}
