<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue;

use Magebees\CacheWarmer\Api\Data\QueuePageInterface;
use Magento\Framework\Model\AbstractModel;

class Page extends AbstractModel implements QueuePageInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magebees\CacheWarmer\Model\Queue\PageFactory $pageFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->pageFactory = $pageFactory;
    }

    protected function _construct()
    {
        $this->_init(\Magebees\CacheWarmer\Model\ResourceModel\Queue\Page::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_getData(QueuePageInterface::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->setData(QueuePageInterface::URL, $url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        return $this->_getData(QueuePageInterface::RATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRate($rate)
    {
        $this->setData(QueuePageInterface::RATE, $rate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->_getData(QueuePageInterface::STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        $this->setData(QueuePageInterface::STORE, $store);

        return $this;
    }

    public function getActivityId(): ?int
    {
        return $this->_getData(QueuePageInterface::ACTIVITY_ID);
    }

    public function setActivityId(?int $activityId): QueuePageInterface
    {
        $this->setData(QueuePageInterface::ACTIVITY_ID, $activityId);

        return $this;
    }
}
