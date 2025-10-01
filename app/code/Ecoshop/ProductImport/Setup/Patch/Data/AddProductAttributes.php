<?php
/**
 * Add custom product attributes for Ersag products
 */
declare(strict_types=1);

namespace Ecoshop\ProductImport\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;

class AddProductAttributes implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Количество, шт (text - варьируется)
        $eavSetup->addAttribute(Product::ENTITY, 'quantity_pcs', [
            'type' => 'varchar',
            'label' => 'Кількість, шт',
            'input' => 'text',
            'required' => false,
            'sort_order' => 10,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'group' => 'Content Attributes'
        ]);

        // Объём (только число в мл)
        $eavSetup->addAttribute(Product::ENTITY, 'volume', [
            'type' => 'decimal',
            'label' => 'Об`єм (мл)',
            'input' => 'text',
            'required' => false,
            'sort_order' => 30,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'group' => 'Content Attributes',
            'validate_rules' => 'a:2:{s:15:"input_validation";s:7:"number";s:12:"locale_allow";s:1:",";}'
        ]);

        // Назначение (select)
        $eavSetup->addAttribute(Product::ENTITY, 'purpose', [
            'type' => 'varchar',
            'label' => 'Призначення',
            'input' => 'select',
            'required' => false,
            'sort_order' => 70,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Мужской', 'Женский', 'Для расчёсывания волос', 'Укрепление и рост волос', 'Пробники']
            ],
            'group' => 'Content Attributes'
        ]);

        // Материал (select)
        $eavSetup->addAttribute(Product::ENTITY, 'material', [
            'type' => 'varchar',
            'label' => 'Матеріал',
            'input' => 'select',
            'required' => false,
            'sort_order' => 80,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Бумага', 'Ткань', 'Хлопок (сатин)', '100% хлопок']
            ],
            'group' => 'Content Attributes'
        ]);

        // Тип кожи (select)
        $eavSetup->addAttribute(Product::ENTITY, 'skin_type', [
            'type' => 'varchar',
            'label' => 'Тип шкіри',
            'input' => 'select',
            'required' => false,
            'sort_order' => 90,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Для жирной кожи', 'Для сухой и нормальной кожи']
            ],
            'group' => 'Content Attributes'
        ]);

        // Типы волос (select)
        $eavSetup->addAttribute(Product::ENTITY, 'hair_type', [
            'type' => 'varchar',
            'label' => 'Типи волосся',
            'input' => 'select',
            'required' => false,
            'sort_order' => 100,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Для всех типов']
            ],
            'group' => 'Content Attributes'
        ]);

        // Возраст (select)
        $eavSetup->addAttribute(Product::ENTITY, 'age_group', [
            'type' => 'varchar',
            'label' => 'Вік',
            'input' => 'select',
            'required' => false,
            'sort_order' => 110,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Для детей']
            ],
            'group' => 'Content Attributes'
        ]);

        // Форма выпускаемого бада (select)
        $eavSetup->addAttribute(Product::ENTITY, 'supplement_form', [
            'type' => 'varchar',
            'label' => 'Форма випуску бада',
            'input' => 'select',
            'required' => false,
            'sort_order' => 120,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['Порошок', 'Капсулы', 'Таблетки', 'Сироп']
            ],
            'group' => 'Content Attributes'
        ]);

        // Форма выпускаемого бада (select)
        $eavSetup->addAttribute(Product::ENTITY, 'quantity_pcs_type', [
            'type' => 'varchar',
            'label' => 'Тип пакування',
            'input' => 'select',
            'required' => false,
            'sort_order' => 120,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'option' => [
                'values' => ['стиков','таблеток','капсул','пакетиков','тестеров']
            ],
            'group' => 'Content Attributes'
        ]);

        return $this;
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
