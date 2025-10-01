<?php
namespace Magebees\Productlabel\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magebees\Productlabel\Helper\Remove
     */
    protected $removeHelper;

    public function __construct(
        \Magebees\Productlabel\Helper\Remove $removeHelper
    ) {
        $this->removeHelper = $removeHelper;
    }
	
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
 
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
			$setup->getConnection()->addColumn(
                $setup->getTable('magebees_productlabel'),
                'cond_serialize',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Conditions'
            );
			$this->removeHelper->removeFolder();
       }
       
		$installer->endSetup();
    }
}
