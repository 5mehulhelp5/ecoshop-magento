<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Setup\Patch\DeclarativeSchemaApplyBefore;

use Magebees\CacheWarmer\Model\Reports as ReportsModel;
use Magebees\CacheWarmer\Model\ResourceModel\Reports as ReportsResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * compatibility with m2.4.2
 * Declarative Schema can not add autoincrement column if existing table does not have primary key.
 */
class PrimaryKeyForReportsTable implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
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
        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable(ReportsResource::TABLE_NAME);
        if ($connection->isTableExists($tableName)
            && !$connection->tableColumnExists($tableName, ReportsModel::REPORT_ID)
        ) {
            $connection->addColumn($tableName, ReportsModel::REPORT_ID, [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'primary' => true,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Report ID'
            ]);
        }
    }
}
