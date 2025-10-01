<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Model\Config\Source\PageType;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

class Refresher
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $configCollectionFactory;
    /**
     * @var QueuePageRepository
     */
    private $pageRepository;
    /**
     * @var Source\PageType\Factory
     */
    private $pageTypeFactory;
    /**
     * @var ResourceModel\Queue\Page
     */
    private $pageResource;

    /**
     * @var Queue\ProcessMetaInfo
     */
    private $processStats;

    /**
     * @var Queue\Combination\Provider
     */
    private $combinationProvider;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Magebees\CacheWarmer\Model\QueuePageRepository $pageRepository,
        \Magebees\CacheWarmer\Model\Source\PageType\Factory $pageTypeFactory,
        \Magebees\CacheWarmer\Model\ResourceModel\Queue\Page $pageResource,
        \Magebees\CacheWarmer\Model\Queue\ProcessMetaInfo $processStats,
        \Magebees\CacheWarmer\Model\Queue\Combination\Provider $combinationProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->pageTypeFactory = $pageTypeFactory;
        $this->pageResource = $pageResource;
        $this->processStats = $processStats;
        $this->combinationProvider = $combinationProvider;
    }

    public function queueIndexPage()
    {
        $type = $this->pageTypeFactory->create(PageType::TYPE_INDEX);
        $this->checkAndQueuePages($type->getAllPages());
    }

    public function queueCmsPage($identifier)
    {
        $filter = function (PageCollection $collection) use ($identifier) {
            $collection->addFieldToFilter('identifier', $identifier);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_CMS, [
            'filterCollection' => $filter
        ]);

        $this->checkAndQueuePages($type->getAllPages());
    }

    public function queueProductPage($id, ?int $storeId = null)
    {
        $filter = function (UrlRewriteCollection $collection) use ($id, $storeId) {
            $collection->addFieldToFilter('product_entity.entity_id', $id);
            $this->addStoreFilter($collection, $storeId);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_PRODUCT, [
            'filterCollection' => $filter
        ]);

        $this->checkAndQueuePages($type->getAllPages(), $storeId);
    }

    public function queueCategoryPage($id, ?int $storeId = null)
    {
        $filter = function (UrlRewriteCollection $collection) use ($id, $storeId) {
            $collection->addFieldToFilter('entity_id', $id);
            $this->addStoreFilter($collection, $storeId);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_CATEGORY, [
            'filterCollection' => $filter
        ]);

        $this->checkAndQueuePages($type->getAllPages(), $storeId);
    }

    public function isIndexPage($identifier)
    {
        if ($identifier == $this->scopeConfig->getValue(PageHelper::XML_PATH_HOME_PAGE)) {
            // Default value
            return true;
        }

        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();

        $configCollection
            ->addFieldToFilter('path', PageHelper::XML_PATH_HOME_PAGE)
            ->addValueFilter($identifier);

        return (bool)$configCollection->getSize();
    }

    protected function checkAndQueuePages(array $pages, ?int $storeId = null)
    {
        $rate = $this->getMaxRate() + 1;

        foreach ($pages as $page) {
            $currentPage = $this->pageRepository->getByUrl($page['url'], $storeId);
            if (!$currentPage->getId()) {
                $currentPage->setData($page);
            }
            $currentPage->setRate($rate);
            $this->pageRepository->save($currentPage);
        }

        $this->processStats->addToTotalPagesQueued(count($pages));
    }

    private function addStoreFilter(UrlRewriteCollection $collection, ?int $storeId)
    {
        $stores = $this->combinationProvider->getCombinationStores();

        if ($storeId) {
            if (!empty($stores)) {
                $stores = array_intersect([$storeId], $stores);
            } else {
                $stores[] = $storeId;
            }
        }

        if (!empty($stores)) {
            $collection->addFieldToFilter('store_id', ['in' => $stores]);
        }
    }

    protected function getMaxRate(): int
    {
        return (int)$this->pageResource->getMaxRate();
    }
}
