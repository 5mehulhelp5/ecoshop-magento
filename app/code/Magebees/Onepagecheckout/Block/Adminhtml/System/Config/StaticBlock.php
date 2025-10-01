<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\System\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;
use Magebees\Onepagecheckout\Model\System\Config\Source\StaticBlockPosition;

class StaticBlock extends AbstractFieldArray
{
    protected $elementFactory;
    protected $blockPosition;
    protected $blockFactory;

    public function __construct(
        Context $context,
        Factory $elementFactory,
        StaticBlockPosition $blockPosition,
        CollectionFactory $blockFactory,
        array $data = []
    )
    {
        $this->elementFactory = $elementFactory;
        $this->blockPosition  = $blockPosition;
        $this->blockFactory   = $blockFactory;

        parent::__construct($context, $data);
    }
    public function _construct()
    {
        $this->addColumn('block', ['label' => __('Block')]);
        $this->addColumn('position', ['label' => __('Position')]);
        $this->addColumn('sort_order', ['label' => __('Sort Order'), 'style' => 'width: 100px']);
        $this->_addAfter       = false;
        $this->_addButtonLabel = __('More');
        parent::_construct();
    }
    public function renderCellTemplate($columnName)
    {
        if (!empty($this->_columns[$columnName])) {
            switch ($columnName) {
                case 'block':
                    $options = $this->blockFactory->create()->toOptionArray();
                    break;
                case 'position':
                    $options = $this->blockPosition->toOptionArray();
                    break;
                default:
                    $options = '';
                    break;
            }
            if ($options) {
                foreach ($options as $index => &$item) {
                    if (is_array($item) && isset($item['label'])) {
                        $item['label'] = addslashes($item['label']);
                    }
                }

                $element = $this->elementFactory->create('select');
                $element->setForm($this->getForm())
                    ->setName($this->_getCellInputElementName($columnName))
                    ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
                    ->setValues($options)
                    ->setStyle('width: 200px');

                return str_replace("\n", '', $element->getElementHtml());
            }
        }
        return parent::renderCellTemplate($columnName);
    }
}