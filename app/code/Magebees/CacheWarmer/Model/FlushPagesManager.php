<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Model\ResourceModel\FlushPages as FlushPagesResource;
use Magebees\CacheWarmer\Model\ResourceModel\FlushPages\CollectionFactory;
use Magebees\CacheWarmer\Model\ResourceModel\FlushPages\Collection;
use Magebees\CacheWarmer\Model\ResourceModel\Log as LogResource;

class FlushPagesManager
{
    /**
     * @var FlushPagesResource
     */
    private $flushPagesResource;

    /**
     * @var LogResource
     */
    private $logResource;

    /**
     * @var \Magebees\CacheWarmer\Model\FlushPagesFactory
     */
    private $flushPagesFactory;

    /**
     * @var CollectionFactory
     */
    private $flushPagesCollectionFactory;

    public function __construct(
        FlushPagesResource $flushPagesResource,
        FlushPagesFactory $flushPagesFactory,
        CollectionFactory $flushPagesCollectionFactory,
        LogResource $logResource
    ) {
        $this->flushPagesResource = $flushPagesResource;
        $this->flushPagesCollectionFactory = $flushPagesCollectionFactory;
        $this->logResource = $logResource;
        $this->flushPagesFactory = $flushPagesFactory;
    }

    /**
     * @param Log $logModel
     */
    public function addPageToFlush($logModel)
    {
        /** @var FlushPages $model */
        $model = $this->flushPagesFactory->create();
        $model->addData(['url' => rtrim($logModel->getData('url'), '/')]);
        $this->flushPagesResource->save($model);

        $this->logResource->delete($logModel);
    }

    /**
     * @param string $url
     *
     * @return bool|FlushPages
     */
    public function findPageToFlush($url)
    {
        /** @var Collection $collection */
        $collection = $this->flushPagesCollectionFactory->create();

        /** @var FlushPages $item */
        $item = $collection->addFieldToFilter('url', rtrim((string)$url, '/'))
            ->setPageSize(1)
            ->getFirstItem();

        if ($item->getData()) {
            return $item;
        }

        return false;
    }

    /**
     * @param FlushPages $model
     */
    public function deletePageToFlush($model)
    {
        $this->flushPagesResource->delete($model);
    }
}
