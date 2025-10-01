<?php
namespace Magebees\Socialsharing\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
		$data = [
			'scope' => 'default',
			'scope_id' => 0,
			'path' => 'dev/js/minify_exclude',
			'value' => 'https://static.addtoany.com/menu/page.js',
		];
		$installer->getConnection()->insertOnDuplicate($installer->getTable('core_config_data'), $data, ['value']);
		$installer->endSetup();
    }
}