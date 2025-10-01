<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magento\Framework\Model\AbstractModel;

class Reports extends AbstractModel
{
    public const REPORT_ID = 'report_id';

    protected function _construct()
    {
        $this->_init(\Magebees\CacheWarmer\Model\ResourceModel\Reports::class);
    }
}
