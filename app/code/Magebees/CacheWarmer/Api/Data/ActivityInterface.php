<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Api\Data;

interface ActivityInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const RATE = 'rate';
    public const STORE = 'store';
    public const URL = 'url';
    public const CURRENCY = 'currency';
    public const CUSTOMER_GROUP = 'customer_group';
    public const MOBILE = 'mobile';
    public const STATUS = 'status';
    public const PAGE_LOAD = 'page_load';
    public const DATE = 'date';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getRate();

    /**
     * @param int $rate
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setRate($rate);

    /**
     * @return int
     */
    public function getStore();

    /**
     * @param int $store
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setStore($store);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setCurrency($currency);

    /**
     * @return int
     */
    public function getCustomerGroup();

    /**
     * @param int $customerGroup
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setCustomerGroup($customerGroup);

    /**
     * @return int
     */
    public function getMobile();

    /**
     * @param int $mobile
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setMobile($mobile);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getDate();

    /**
     * @param int $date
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function setDate($date);
}
