<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Api\Data\QueuePageInterface;
use Magebees\CacheWarmer\Api\QueuePageRepositoryInterface;

class QueuePageRepository implements QueuePageRepositoryInterface
{
    /**
     * @var ResourceModel\Queue\Page
     */
    private $pageResource;
    /**
     * @var Queue\PageFactory
     */
    private $pageFactory;

    public function __construct(
        ResourceModel\Queue\Page $pageResource,
        Queue\PageFactory $pageFactory
    ) {
        $this->pageResource = $pageResource;
        $this->pageFactory = $pageFactory;
    }

    public function delete(QueuePageInterface $entity)
    {
        $this->pageResource->delete($entity);
    }

    public function save(QueuePageInterface $entity)
    {
        $this->pageResource->save($entity);
    }

    public function getByUrl(string $url, ?int $storeId = null): QueuePageInterface
    {
        $page = $this->pageFactory->create();
        $pageId = $this->pageResource->getPageByUrl($url, $storeId);
        if (!$pageId) {
            return $page;
        }
        $this->pageResource->load($page, $pageId);

        return $page;
    }

    public function addPage($pageData)
    {
        /** @var Queue\Page $page */
        $page = $this->pageFactory->create();

        $page->setData($pageData);

        $this->save($page);

        return $page;
    }

    public function clear()
    {
        $this->pageResource->truncate();
    }
}
