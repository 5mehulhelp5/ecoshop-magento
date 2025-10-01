<?php
namespace Magebees\Ajaxsearch\Model\Config;

class AttributeField extends \Magento\Config\Block\System\Config\Form\Fieldset
{
     /**
      * @var \Magento\Framework\DataObject
      */
    protected $_dummyElement;

    /**
     * @var \Magento\Config\Block\System\Config\Form\Field
     */
    protected $_fieldRenderer;

    /**
     * @var array
     */
    protected $_values;

      /**
       * @param \Magento\Backend\Block\Context $context
       * @param \Magento\Backend\Model\Auth\Session $authSession
       * @param \Magento\Framework\View\Helper\Js $jsHelper
       * @param \Magento\Framework\Module\ModuleListInterface $moduleList
       * @param array $data
       */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->attributeCollection=$attributeCollection;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);
        
        /*get searchable attribute from attribute model*/
        $attributes=$this->attributeCollection->getData();
        $attribute_arr['0']='-----Select Attribute-----';
        sort($attributes);
        foreach ($attributes as $attribute) {
            if ($attribute['is_searchable']) {
                $attribute_arr[$attribute['attribute_id']]=$attribute['frontend_label'];
                $html .= $this->_getFieldHtml($element, $attribute['frontend_label'], $attribute['attribute_id'], $attribute['search_weight']);
            }
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new \Magento\Framework\DataObject(['showInDefault' => 1, 'showInWebsite' => 0,'showInStore'=> 0]);
        }
        
        return $this->_dummyElement;
    }

    /**
     * @return \Magento\Config\Block\System\Config\Form\Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = $this->_layout->getBlockSingleton(
                'Magento\Config\Block\System\Config\Form\Field'
            );
        }
        return $this->_fieldRenderer;
    }

    /**
     * @return array
     */
    protected function _getValues()
    {
        if (empty($this->_values)) {
            $this->_values = [
                ['label' => __('1'), 'value' => 1],
                ['label' => __('2'), 'value' => 2],
                ['label' => __('3'), 'value' => 3],
                ['label' => __('4'), 'value' => 4],
                ['label' => __('5'), 'value' => 5],
                ['label' => __('6'), 'value' => 6],
                ['label' => __('7'), 'value' => 7],
                ['label' => __('8'), 'value' => 8],
                ['label' => __('9'), 'value' => 9],
                ['label' => __('10'), 'value' => 10],
            ];
        }
        return $this->_values;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param string $moduleName
     * @return mixed
     */
    protected function _getFieldHtml($fieldset, $attrName, $attr_id, $search_weight)
    {
        $configData = $this->getConfigData();
        
        $path = 'ajaxsearch/attributes/' . lcfirst($attrName).'_'.$attr_id;
        //TODO: move as property of form
        $data = $search_weight;
        $inherit = true;
        
        $element = $this->_getDummyElement();
        $field = $fieldset->addField(
            'ajaxsearch/attributes/' .lcfirst($attrName).'_'.$attr_id,
            'select',
            [
                'name' => 'groups[attributes][fields][' .lcfirst($attrName).'_'.$attr_id . '][value]',
                'label' => $attrName,
                'value' => $data,
                'values' => $this->_getValues(),
                'inherit' => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($element),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($element)
            ]
        )->setRenderer(
            $this->_getFieldRenderer()
        );

        return $field->toHtml();
    }
}
