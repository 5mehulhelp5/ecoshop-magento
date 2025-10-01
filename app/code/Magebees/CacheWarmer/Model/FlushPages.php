<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magento\Framework\Model\AbstractModel;

class FlushPages extends AbstractModel
{
    public const ID = 'id';
    public const URL = 'url';

    public function _construct()
    {
        $this->_init(ResourceModel\FlushPages::class);
    }
}
