<?php
namespace Magebees\Pagebanner\Setup;

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
            ->newTable($installer->getTable('magebees_pagebanner'))
            ->addColumn(
                'banner_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Banner ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('stores', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('status', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('page_type_options', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('cms_page', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('catalog_category', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('blog_category', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('layout_handle', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('banner_image', Table::TYPE_TEXT, 255, ['nullable' => false])
            // Adding the new columns for image width and height
            ->addColumn('banner_image_width', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Banner Image Width')
            ->addColumn('banner_image_height', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Banner Image Height')
            ->setComment('Magebees Page Banner Details');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
