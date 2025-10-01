<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */
namespace Magebees\CacheWarmer\Api;

use Magebees\CacheWarmer\Api\Data\QueuePageInterface;

interface QueuePageRepositoryInterface
{
    public function delete(QueuePageInterface $entity);

    public function save(QueuePageInterface $entity);

    /**
     * @param array $pageData
     *
     * @return \Magebees\CacheWarmer\Model\Queue\Page mixed
     */
    public function addPage($pageData);

    public function clear();
}
