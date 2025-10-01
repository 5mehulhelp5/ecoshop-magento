<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Setup;

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
            ->newTable($installer->getTable('magebees_productlabel'))
            ->addColumn(
                'label_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Label ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('is_active', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('hide', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('sort_order', Table::TYPE_SMALLINT, 6, ['nullable' => false])
            ->addColumn('stores', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('customer_group_ids', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('prod_text', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('prod_image', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('prod_image_width', Table::TYPE_INTEGER, ['nullable' => false])
            ->addColumn('prod_image_height', Table::TYPE_INTEGER, ['nullable' => false])
            ->addColumn('state', Table::TYPE_TEXT, 100, ['nullable' => false])
            ->addColumn('prod_position', Table::TYPE_TEXT, 2, ['nullable' => false])
            ->addColumn('prod_text_color', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('prod_text_size', Table::TYPE_INTEGER, ['nullable' => false])
            ->addColumn('cat_text', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('cat_image', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('cat_image_width', Table::TYPE_INTEGER, ['nullable' => false])
            ->addColumn('cat_image_height', Table::TYPE_INTEGER, ['nullable' => false])
            ->addColumn('cat_position', Table::TYPE_TEXT, 2, ['nullable' => false])
            ->addColumn('cat_text_color', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('cat_text_size', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('include_sku', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('include_cat', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('attr_code', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('attr_value', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('is_new', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('is_sale', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('date_enabled', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('from_date', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null])
            ->addColumn('to_date', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null])
            ->addColumn('price_enabled', Table::TYPE_BOOLEAN, ['nullable' => false])
            ->addColumn('from_price', Table::TYPE_DECIMAL, '12,4', ['nullable' => false])
            ->addColumn('to_price', Table::TYPE_DECIMAL, '12,4', ['nullable' => false])
            ->addColumn('by_price', Table::TYPE_SMALLINT, 4, ['nullable' => false])
            ->addColumn('stock_status', Table::TYPE_SMALLINT, 4, ['nullable' => false])
            ->setComment('Magebees Product Label Details');

        $installer->getConnection()->createTable($table);
               
        $installer->endSetup();
    }
}
