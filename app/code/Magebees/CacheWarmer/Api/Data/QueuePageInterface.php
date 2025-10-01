<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Api\Data;

interface QueuePageInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const URL = 'url';
    public const RATE = 'rate';
    public const STORE = 'store';
    public const ACTIVITY_ID = 'activity_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Magebees\CacheWarmer\Api\Data\QueuePageInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     *
     * @return \Magebees\CacheWarmer\Api\Data\QueuePageInterface
     */
    public function setUrl($url);

    /**
     * @return int
     */
    public function getRate();

    /**
     * @param int $rate
     *
     * @return \Magebees\CacheWarmer\Api\Data\QueuePageInterface
     */
    public function setRate($rate);

    /**
     * @return int|null
     */
    public function getStore();

    /**
     * @param int|null $store
     *
     * @return \Magebees\CacheWarmer\Api\Data\QueuePageInterface
     */
    public function setStore($store);

    public function getActivityId(): ?int;

    public function setActivityId(?int $activityId): QueuePageInterface;
}
