<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Setup;

use Magebees\CacheWarmer\Model\BackgroundJob\ResourceModel\Job;
use Magebees\CacheWarmer\Model\ResourceModel\Activity;
use Magebees\CacheWarmer\Model\ResourceModel\DebugContext;
use Magebees\CacheWarmer\Model\ResourceModel\FlushesLog;
use Magebees\CacheWarmer\Model\ResourceModel\FlushPages;
use Magebees\CacheWarmer\Model\ResourceModel\Log;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page;
use Magebees\CacheWarmer\Model\ResourceModel\Reports;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->uninstallTables($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            Activity::TABLE_NAME,
            DebugContext::TABLE_NAME,
            FlushPages::TABLE_NAME,
            Job::TABLE_NAME,
            Log::TABLE_NAME,
            Page::TABLE_NAME,
            Reports::TABLE_NAME,
            FlushesLog::TABLE_NAME
        ];

        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $configTable = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($configTable, "`path` LIKE 'magebees_cachewarmer%'");

        return $this;
    }
}
