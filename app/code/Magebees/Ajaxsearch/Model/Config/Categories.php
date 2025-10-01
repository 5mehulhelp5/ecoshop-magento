<?php

namespace Magebees\Ajaxsearch\Model\Config;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    protected $_systemStore;
    protected $_categorytree;
    protected $_categoryFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categorytree,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_categorytree = $categorytree;
        $this->_categoryFactory = $categoryFactory;
    }
    public function buildCategoriesMultiselectValues($node, $values, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $level++;
        if ($level > 2) {
            if ($node->getIsActive()) {
                $values[$node->getId()]['value'] = $node->getId();
                $values[$node->getId()]['label'] = str_repeat($nonEscapableNbspChar, ($level - 3) * 5).$node->getName();
            }
        }

        foreach ($node->getChildren() as $child) {
            if ($child->getIsActive()) {
                $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
            }
        }

        return $values;
    }

    public function toOptionArray()
    {
        $tree = $this->_categorytree->load();

        $parentId = 1;

        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName('Root');
        }

        $collection = $this->_categoryFactory->create()->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');
        $tree->addCollectionData($collection, true);

        $values['---'] = [
            'value' => '0',
            'label' => '-------Select Category-------',
        ];
        return $this->buildCategoriesMultiselectValues($root, $values);
    }
}
