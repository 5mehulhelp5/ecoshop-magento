<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Setup\Patch\DeclarativeSchemaApplyBefore;

use Magebees\CacheWarmer\Api\Data\ActivityInterface;
use Magebees\CacheWarmer\Model\ResourceModel\Activity as ActivityResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DeleteDuplicateRowsInActivityTable implements DataPatchInterface
{
    /**
     * @var ActivityResource
     */
    private $activityResource;

    public function __construct(
        ActivityResource $activityResource
    ) {
        $this->activityResource = $activityResource;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $connection = $this->activityResource->getConnection();
        $activityTable = $this->activityResource->getTable(ActivityResource::TABLE_NAME);
        if ($connection->isTableExists($activityTable) && $this->updateDuplicateRates($connection)) {
            $this->deleteDuplicateUrls($connection);
        }
    }

    private function updateDuplicateRates(AdapterInterface $connection): bool
    {
        $isUpdated = false;
        $selectDuplicates = $connection->select()->from(
            $this->activityResource->getMainTable(),
            [
                ActivityInterface::ID,
                ActivityInterface::RATE => new \Zend_Db_Expr('SUM(rate)')
            ]
        )->group(
            [ActivityInterface::URL, ActivityInterface::MOBILE]
        )->having(
            new \Zend_Db_Expr('COUNT(url) > 1')
        );

        if (!empty($connection->fetchAll($selectDuplicates))) {
            $connection->insertOnDuplicate(
                $this->activityResource->getMainTable(),
                $connection->fetchAll($selectDuplicates)
            );
            $isUpdated = true;
        }

        return $isUpdated;
    }

    private function deleteDuplicateUrls(AdapterInterface $connection): void
    {
        $table1 = ['T1' => $this->activityResource->getMainTable()];
        $table2 = ['T2' => $this->activityResource->getMainTable()];
        $joinCond = implode(
            ' AND ',
            ['T1.url = T2.url', 'T1.mobile = T2.mobile', 'T2.id < T1.id']
        );

        $select = $connection->select()
            ->from($table1, ['T1.id'])
            ->join($table2, $joinCond, [])
            ->group(ActivityInterface::ID);

        $connection->delete(
            $table1,
            'id IN (' . implode(',', $connection->fetchCol($select)) . ')'
        );
    }
}
