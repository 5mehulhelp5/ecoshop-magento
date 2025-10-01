<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Api;

/**
 * @api
 */
interface ActivityRepositoryInterface
{
    /**
     * Save
     *
     * @param \Magebees\CacheWarmer\Api\Data\ActivityInterface $activity
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     */
    public function save(\Magebees\CacheWarmer\Api\Data\ActivityInterface $activity);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Magebees\CacheWarmer\Api\Data\ActivityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Magebees\CacheWarmer\Api\Data\ActivityInterface $activity
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magebees\CacheWarmer\Api\Data\ActivityInterface $activity);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
