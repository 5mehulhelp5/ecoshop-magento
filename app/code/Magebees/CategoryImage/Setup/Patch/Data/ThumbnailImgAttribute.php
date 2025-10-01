<?php
namespace Magebees\CategoryImage\Setup\Patch\Data;

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
class ThumbnailImgAttribute implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
	private $attributeRepository;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
		 $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'thumbnail', [
                    'type' => 'varchar',
                    'label' => 'Thumbnail',
                    'input' => 'image',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'required' => false,
                    'sort_order' => 6,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           );
		   
		    
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'circle_title', [
                    'type' => 'varchar',
                    'label' => 'Circle Title',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 7,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           );
		   $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'circle_discount', [
                    'type' => 'varchar',
                    'label' => 'Circle Discount',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 8,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           );
		   $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'discount_code_label', [
                    'type' => 'varchar',
                    'label' => 'Discount Code Label',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 9,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           );
		   
		  
		   $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'discount_code', [
                    'type' => 'varchar',
                    'label' => 'Discount Code',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           ); 
		   $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_label_text', [
                    'type' => 'varchar',
                    'label' => 'Category Label Text',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 11,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           ); 
		   $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'show_product_counter', [
                    'type' => 'int',
                    'label' => 'Show Product Counter',
                    'input' => 'boolean',
                    'required' => false,
					'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'sort_order' => 12,
					'default'  => '1',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
           ); 
		  
		$this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'thumbnail');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'circle_title');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'circle_discount');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'discount_code');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'discount_code_label');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_label_text');
		$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'show_product_counter');
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
