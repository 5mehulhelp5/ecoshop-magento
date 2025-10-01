<?php
namespace Magebees\Onepagecheckout\Setup\Patch\Data;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magebees\Onepagecheckout\Model\SuccesscustomFactory;
use Magento\Framework\App\ResourceConnection;

class AddData implements DataPatchInterface, PatchRevertableInterface
{
	private $moduleDataSetup;
	private $_postFactory;
	private $blockFactory;
	private $ResourceConnection;

	public function __construct(
		SuccesscustomFactory $postFactory,
		BlockFactory $blockFactory,
		ModuleDataSetupInterface $moduleDataSetup,
		ResourceConnection $ResourceConnection
	) {
		$this->moduleDataSetup = $moduleDataSetup;
		$this->_postFactory = $postFactory;
		$this->blockFactory = $blockFactory;
		$this->ResourceConnection = $ResourceConnection;
	}
	public function apply()
	{
		$this->moduleDataSetup->startSetup();
		$connection = $this->ResourceConnection->getConnection();
		$tableName = $this->ResourceConnection->getTableName('cws_magebees_ocp_successcustom');
		$sql = "SELECT * FROM " . $tableName;
		$result = $connection->fetchAll($sql); 
		
		if(empty($result)){
			$successData =  [
							[1, 'mockTop', 'block1', 'CMS Block #1'],
							[2, 'mockLeft', 'thankUnote', 'Thank You Note'],
							[3, 'mockLeft', 'quickReg', 'Quick Registration'],
							[4, 'mockRight', 'orderDetails', 'Order Details'],
							[5, 'mockRight', 'shipingPayment', 'Shipping & Payment Info'],
							[6, 'mockRight', 'additionalInfo', 'Additional Info'],
							[7, 'mockBottom', 'block2', 'CMS Block #2'],
							[8, 'mockAll', 'block3', 'CMS Block #3'],
							[9, 'mockAll', 'block4', 'CMS Block #4']
						];
			
			foreach ($successData as $cdata) {
				$data = [
					'oscsection' => $cdata[1],
					'oscfieldname' => $cdata[2],
					'oscfieldvalue' => $cdata[3]
				];
				$post = $this->_postFactory->create();
				$post->addData($data)->save();
			}
		}
		$cmsBlock1 = $this->blockFactory->create();
        $OpcBlocl1 = $cmsBlock1->load('opc_cms_block_1','identifier');

        if (!$OpcBlocl1->getId()) {
            $OpcBlocl1 = [
                'title' => 'opc_cms_block_1',
                'identifier' => 'opc_cms_block_1',
                'stores' => [0],
                'content' => "opc_cms_block_1",
                'is_active' => 1,
            ];
            $cmsBlock1->setData($OpcBlocl1)->save();
        }
        $cmsBlock2 = $this->blockFactory->create();
        $OpcBlocl2 = $cmsBlock2->load('opc_cms_block_2','identifier');
        if (!$OpcBlocl2->getId()) {
            $OpcBlocl2 = [
                'title' => 'opc_cms_block_2',
                'identifier' => 'opc_cms_block_2',
                'stores' => [0],
                'content' => "opc_cms_block_2",
                'is_active' => 1,
            ];
            $cmsBlock2->setData($OpcBlocl2)->save();
        }
        $cmsBlock3 = $this->blockFactory->create();
        $OpcBlocl3 = $cmsBlock3->load('opc_cms_block_3','identifier');
        if (!$OpcBlocl3->getId()) {
            $OpcBlocl3 = [
                'title' => 'opc_cms_block_3',
                'identifier' => 'opc_cms_block_3',
                'stores' => [0],
                'content' => "opc_cms_block_3",
                'is_active' => 1,
            ];
            $cmsBlock3->setData($OpcBlocl3)->save();
        }
        $cmsBlock4 = $this->blockFactory->create();
        $OpcBlocl4 = $cmsBlock4->load('opc_cms_block_4','identifier');
        if (!$OpcBlocl4->getId()) {
            $OpcBlocl4 = [
                'title' => 'opc_cms_block_4',
                'identifier' => 'opc_cms_block_4',
                'stores' => [0],
                'content' => "opc_cms_block_4",
                'is_active' => 1,
            ];
            $cmsBlock4->setData($OpcBlocl4)->save();
        }
		$this->moduleDataSetup->endSetup();
	}
	public function revert()
    {
		$sampleCmsBlock = $this->blockFactory
			->create()
			->load(self::CMS_BLOCK_IDENTIFIER, 'opc_cms_block_1');

		if ($sampleCmsBlock->getId()) {
			$sampleCmsBlock->delete();
		}

		$sampleCmsBlock = $this->blockFactory
			->create()
			->load(self::CMS_BLOCK_IDENTIFIER, 'opc_cms_block_2');

		if ($sampleCmsBlock->getId()) {
			$sampleCmsBlock->delete();
		}

		$sampleCmsBlock = $this->blockFactory
			->create()
			->load(self::CMS_BLOCK_IDENTIFIER, 'opc_cms_block_3');

		if ($sampleCmsBlock->getId()) {
			$sampleCmsBlock->delete();
		}
		
		$sampleCmsBlock = $this->blockFactory
			->create()
			->load(self::CMS_BLOCK_IDENTIFIER, 'opc_cms_block_4');

		if ($sampleCmsBlock->getId()) {
			$sampleCmsBlock->delete();
		}
    }
	public function getAliases()
    {
        return [];
    }
    public static function getDependencies()
    {
        return [];
    }
}