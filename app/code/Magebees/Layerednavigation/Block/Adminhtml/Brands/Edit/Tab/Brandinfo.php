<?php
namespace Magebees\Layerednavigation\Block\Adminhtml\Brands\Edit\Tab;

class Brandinfo extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_systemStore;
    protected $_brands;
    protected $_yesno;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_yesno = $yesno;
        $this->_brands = $brands;
        parent::__construct($context, $registry, $formFactory, $data);
    }

     
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('brands_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('main_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Detail')]);

        if ($model->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
        }

        $brand_name = $fieldset->addField(
            'brand_name',
            'label',
            [
                'name' => 'brand_name',
                'label' => __('Brand Name'),
                'title' => __('Brand Name'),
            ]
        );

        $brand_code = $fieldset->addField(
            'brand_code',
            'label',
            [
                'name' => 'brand_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
            ]
        );
        
        $brand_description = $fieldset->addField(
            'brand_description',
            'textarea',
            [
                'name' => 'brand_description',
                'label' => __('Brand Tooltip Description'),
                'title' => __('Brand Tooltip Description'),
            ]
        );
        $filename = $fieldset->addField(
            'filename',
            'image',
            [
                'name' => 'filename',
                'label' => __('Brand Logo'),
                'title' => __('Brand Logo'),
            ]
        );
        
        $status=$fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => [
                    '1' => __('Enabled'),
                    '0' => __('Disabled'),
                ],
            ]
        );
        $sort_order = $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'class'     => 'validate-number',
            ]
        );
        $featuredbrand = $fieldset->addField(
            'featuredbrand',
            'select',
            [
                'name' => 'featuredbrand',
                'label' => __('Featured Brand'),
                'title' => __('Featured Brand'),
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
        $model_data = $model->getData();
        if (count($model_data)>0) {
            if ($model_data['filename'] != "") {
                $imgpath = "layernav_brand".$model_data['filename'];
                array_push($model_data, $model_data['filename'] = $imgpath);
            }
        }
        $form->setValues($model_data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Brand Detail');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Brand Detail');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
