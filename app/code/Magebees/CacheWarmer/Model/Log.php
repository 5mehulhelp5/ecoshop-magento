<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @method ResourceModel\Log getResource()
 */
class Log extends AbstractModel
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ResourceModel\Log\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LogFactory
     */
    private $logFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Config $config,
        \Magebees\CacheWarmer\Model\ResourceModel\Log\CollectionFactory $collectionFactory,
        LogFactory $logFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->logFactory = $logFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Log::class);
    }

    /**
     * Delete all records that exceeds "Log Size" limit
     *
     * @return $this
     */
    public function trim()
    {
        $maxSize = $this->config->getLogSize();

        /** @var ResourceModel\Log\Collection $collection */
        $collection = $this->collectionFactory->create();

        $limit = $collection->getSize() - $maxSize;
        //phpcs:ignore Magento2.Methods.DeprecatedModelMethod.FoundDeprecatedModelMethod
        $this->getResource()->deleteWithLimit($limit);

        return $this;
    }

    public function add($data)
    {
        /** @var Log $record */
        $record = $this->logFactory->create();

        $record->setData($data);
        //phpcs:ignore Magento2.Methods.DeprecatedModelMethod.FoundDeprecatedModelMethod
        $this->getResource()->save($record);

        return $this;
    }
}
