<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Repository;

use Magebees\CacheWarmer\Api\Data\FlushesLogInterface;
use Magebees\CacheWarmer\Api\FlushesLogRepositoryInterface;
use Magebees\CacheWarmer\Model\FlushesLog as FlushesLogModel;
use Magebees\CacheWarmer\Model\FlushesLogFactory;
use Magebees\CacheWarmer\Model\ResourceModel\FlushesLog as FlushesLogResource;
use Magebees\CacheWarmer\Model\ResourceModel\FlushesLog\Collection;
use Magebees\CacheWarmer\Model\ResourceModel\FlushesLog\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class FlushesLogRepository implements FlushesLogRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var FlushesLogFactory
     */
    private $flushesLogFactory;

    /**
     * @var FlushesLogResource
     */
    private $flushesLogResource;

    /**
     * @var CollectionFactory
     */
    private $flushesLogCollectionFactory;

    /**
     * @var array
     */
    private $flushesLog;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        FlushesLogFactory $flushesLogFactory,
        FlushesLogResource $flushesLogResource,
        CollectionFactory $flushesLogCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->flushesLogFactory = $flushesLogFactory;
        $this->flushesLogResource = $flushesLogResource;
        $this->flushesLogCollectionFactory = $flushesLogCollectionFactory;
    }

    public function save(FlushesLogInterface $flushesLog)
    {
        try {
            $this->flushesLogResource->save($flushesLog);
            unset($this->flushesLog[$flushesLog->getLogId()]);
        } catch (\Exception $e) {
            if ($flushesLog->getLogId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save log with ID %1. Error: %2',
                        [$flushesLog->getLogId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new flushes log. Error: %1', $e->getMessage()));
        }

        return $flushesLog;
    }

    public function getById($id)
    {
        if (!isset($this->flushesLog[$id])) {
            /** @var FlushesLogModel $flushesLog */
            $flushesLog = $this->flushesLogFactory->create();
            $this->flushesLogResource->load($flushesLog, $id);
            if (!$flushesLog->getLogId()) {
                throw new NoSuchEntityException(__('Flushes log with specified ID "%1" not found.', $id));
            }
            $this->flushesLog[$id] = $flushesLog;
        }

        return $this->flushesLog[$id];
    }

    public function delete(FlushesLogInterface $flushesLog)
    {
        try {
            $this->flushesLogResource->delete($flushesLog);
            unset($this->flushesLog[$flushesLog->getLogId()]);
        } catch (\Exception $e) {
            if ($flushesLog->getLogId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove log with ID %1. Error: %2',
                        [$flushesLog->getLogId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove log. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById($id)
    {
        $flushesLogModel = $this->getById($id);
        $this->delete($flushesLogModel);

        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $flushesLogCollection */
        $flushesLogCollection = $this->flushesLogCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $flushesLogCollection);
        }

        $searchResults->setTotalCount($flushesLogCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $flushesLogCollection);
        }

        $flushesLogCollection->setCurPage($searchCriteria->getCurrentPage());
        $flushesLogCollection->setPageSize($searchCriteria->getPageSize());

        $flushesLogs = [];
        /** @var FlushesLogInterface $flushesLog */
        foreach ($flushesLogCollection->getItems() as $flushesLog) {
            $flushesLogs[] = $this->getById($flushesLog->getId());
        }

        $searchResults->setItems($flushesLogs);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $flushesLogCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $flushesLogCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $flushesLogCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $flushesLogCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $flushesLogCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $flushesLogCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
