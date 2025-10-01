<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\PageType;

use Magebees\CacheWarmer\Model\Queue\Combination\Provider;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Url as FrontendUrlBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Cms extends Emulated
{
    /**
     * @var PageCollection
     */
    private $pageCollection;

    public function __construct(
        PageCollectionFactory $pageCollectionFactory,
        FrontendUrlBuilder $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        Provider $combinationProvider,
        $isMultiStoreMode = false,
        array $stores = [],
        ?\Closure $filterCollection = null
    ) {
        parent::__construct(
            $urlBuilder,
            $appEmulation,
            $appState,
            $storeManager,
            $combinationProvider,
            $isMultiStoreMode,
            $stores,
            $filterCollection
        );
        $this->pageCollection = $pageCollectionFactory->create();
        $this->pageCollection->addFieldToFilter('is_active', true);
    }

    /**
     * @param $storeId
     *
     * @return PageCollection
     */
    protected function getEntityCollection(int $storeId): PageCollection
    {
        return $this->pageCollection;
    }

    /**
     * @param $entity
     * @param $storeId
     *
     * @return bool|string
     */
    protected function getUrl($entity, $storeId)
    {
        if ($this->isMultiStoreMode
            && !in_array(Store::DEFAULT_STORE_ID, $entity->getStores())
            && !in_array($storeId, $entity->getStores())
        ) {
            // Page is not visible for this store
            return false;
        } else {
            return $entity->getIdentifier() . '/';
        }
    }
}
