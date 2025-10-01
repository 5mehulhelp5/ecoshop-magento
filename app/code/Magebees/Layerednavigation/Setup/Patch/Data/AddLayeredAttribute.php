<?php
namespace Magebees\Layerednavigation\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AddOutofstockProductAttribute
 *
 * @package Vendor\Module\Setup\Patch\Data
 */
class AddLayeredAttribute implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    protected $productAttributeCollectionFactory;
    protected $attr_helper;
    protected $helper;
    protected $layerattributeFactory;
    protected $attributeoptionFactory;
    protected $state;


    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        \Magento\Framework\App\State $state,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->attr_helper = $attr_helper;
        $this->helper = $helper;
        $this->layerattributeFactory = $layerattributeFactory;
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        
        $this->moduleDataSetup->getConnection()->startSetup();
        /*for add brand attribute in catalog attribute list*/
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
      
	  
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'docer_layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'footwear_layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'megastore_layernav_brand');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'layernav_brand',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Brand',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => true,
                'is_visible_in_advanced_search' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'user_defined'  => true
            ]
        );
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'docer_layernav_brand',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Brand',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => true,
                'is_visible_in_advanced_search' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'user_defined'  => true
            ]
        );
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'footwear_layernav_brand',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Brand',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => true,
                'is_visible_in_advanced_search' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'user_defined'  => true
            ]
        );
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'megastore_layernav_brand',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Brand',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => true,
                'is_visible_in_advanced_search' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'user_defined'  => true
            ]
        );
         $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'layernav_brand', 'is_visible_in_advanced_search', 1);
        $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'docer_layernav_brand', 'is_visible_in_advanced_search', 1);
		$eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'footwear_layernav_brand', 'is_visible_in_advanced_search', 1);
		$eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'megastore_layernav_brand', 'is_visible_in_advanced_search', 1);
		
        
        /*for insert filterable attribute in custom table*/
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $collection=$productAttributes->addFieldToFilter('is_filterable', ['gt' => 0])
        ->addFieldToFilter('is_visible', '1')->addFieldToFilter('is_user_defined', '1');
        
        foreach ($collection as $coll) {
            $data = [
            'attribute_id' => $coll['attribute_id'],
            'display_mode'=>'0',
            'show_product_count'=>'1',
            'show_searchbox'=>'0',
            'show_in_block'=>'0',
            'unfold_option'=>'4',
            'always_expand'=>'0',
            'sort_option'=>'0',
            'tooltip'=>'',
            'robots_nofollow'=>'0',
            'robots_noindex'=>'0',
            'rel_nofollow'=>'0'
                    
                    ];
 
                    $post = $this->layerattributeFactory->create();
 
                    $post->setData($data)->save();
        }
        
        /*for insert filterable attribute option in 'magebees_layernav_attribute_option' table*/
        
        $attributes=$this->attr_helper->getFilterableAttributes();
        
        foreach ($attributes as $a) {
            $attr_code        = $a['attribute_code'];
            $attr_id        = $a['attribute_id'];
            $options = $this->attr_helper->getAllOptions($attr_code);
           
            
            foreach ($options as $o) {
               
            
                $url_alias=$this->helper->urlAliasAfterReplaceChar($o['label']);
                $main_url_alias=trim(strtolower($o['label']));           
                $option_label = $o['label'];
                $option_id = $o['value'];
                  $opt_data = [
                            'attribute_id' =>$attr_id,
                            'option_id'=>$option_id,
                            'url_alias'=>$url_alias,
                            'main_url_alias'=>$main_url_alias,
                            'option_label'=>$option_label
                        ];
             
                  $post = $this->attributeoptionFactory->create();
                  $post->setData($opt_data)->save();
            }
        }

    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'docer_layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'footwear_layernav_brand');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'megastore_layernav_brand');


        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
        
        ];
    }
}
