<?php
namespace Magebees\EmailEditor\Setup\Patch\Data;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

class AddEmailCmsStaticBlock implements DataPatchInterface, PatchRevertableInterface

{

    private $moduleDataSetup;

    private $blockFactory;

    public function __construct(

        ModuleDataSetupInterface $moduleDataSetup,

        BlockFactory $blockFactory

    ) {

        $this->moduleDataSetup = $moduleDataSetup;

        $this->blockFactory = $blockFactory;

    }


    public function apply()

    {

         $customCmsBlockData1 = [
            'title' => 'Magebees Custom CMS Block 1',
            'identifier' => 'magebees_custom_block_1',
            'content' => '{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" show_pager="0" products_count="4" template="Magebees_EmailEditor::content/grid.phtml" conditions_encoded="^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^]^]"}}',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];




        $this->moduleDataSetup->startSetup();

 

        /** @var \Magento\Cms\Model\Block $block */

        $block = $this->blockFactory->create();

        $block->setData($customCmsBlockData1)->save();

 

	$customCmsBlockData2 = [
            'title' => 'Magebees Custom CMS Block 2',
            'identifier' => 'magebees_custom_block_2',
            'content' => '<p class="mail-banner"><img src="{{view url="Magebees_EmailEditor/images/sale.jpg"}}" alt="Sale" width="1500" height="150" /></p>',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];


	$block = $this->blockFactory->create();

        $block->setData($customCmsBlockData2)->save();


	 $socialMediaBlockData = [
            'title' => 'Magebees Social Media Block',
            'identifier' => 'magebees_social_media_block',
            'content' => '<ul class="btn-social">
<li><a href="https://www.facebook.com/" target="_blank"><img src="{{view url="Magebees_EmailEditor/images/facebook.png"}}" alt="facebook" width="30" height="30" /></a></li>
<li><a href="https://twitter.com/" target="_blank"><img src="{{view url="Magebees_EmailEditor/images/twitter.png"}}" alt="Twitter" width="30" height="30" /></a></li>
<li><a href="https://plus.google.com/" target="_blank"><img src="{{view url="Magebees_EmailEditor/images/google-plus.png"}}" alt="Google plus" width="30" height="30" /></a></li>
<li><a href="https://in.linkedin.com/" target="_blank"><img src="{{view url="Magebees_EmailEditor/images/instagram.png"}}" alt="Instagram" width="30" height="30" /></a></li>
</ul>',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];


	$block = $this->blockFactory->create();

        $block->setData($socialMediaBlockData)->save();

        $this->moduleDataSetup->endSetup();

    }

  public static function getVersion()

    {

        return '1.0.2';

    }


   public function revert()
    {
        $sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_BLOCK_IDENTIFIER, 'magebees_custom_block_1');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }

	$sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_BLOCK_IDENTIFIER, 'magebees_custom_block_2');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }

	$sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_BLOCK_IDENTIFIER, 'magebees_social_media_block');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }
    }


    public static function getDependencies()

    {

        return [];

    }

    

    public function getAliases()

    {

        return [];

    }

}
