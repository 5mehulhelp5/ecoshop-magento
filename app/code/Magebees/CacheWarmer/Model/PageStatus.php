<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

class PageStatus
{
    public const STATUS_UNDEFINED = 'undefined';
    public const STATUS_HIT = 'hit';
    public const STATUS_MISS = 'miss';
    public const STATUS_IGNORED = 'ignored';

    /**
     * @var string
     */
    protected $status = self::STATUS_HIT;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
