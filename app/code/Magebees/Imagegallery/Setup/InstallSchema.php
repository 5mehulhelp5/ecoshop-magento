<?php
namespace Magebees\Imagegallery\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('magebees_imagegallery'))
            ->addColumn(
                'image_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Image ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false])
			->addColumn('stores', Table::TYPE_TEXT, 255, ['nullable' => false])
			->addColumn('status', Table::TYPE_BOOLEAN, ['nullable' => false])
			->addColumn('isexternal', Table::TYPE_BOOLEAN, ['nullable' => false])
			->addColumn('url', Table::TYPE_TEXT, 255, ['nullable' => false])
			->addColumn('sort_order', Table::TYPE_TEXT, 255, ['nullable' => false])
			->addColumn('image', Table::TYPE_TEXT, 255, ['nullable' => false])
			->setComment('Magebees Gallery Details');
        $installer->getConnection()->createTable($table);
        
		$installer->endSetup();
    }
}
