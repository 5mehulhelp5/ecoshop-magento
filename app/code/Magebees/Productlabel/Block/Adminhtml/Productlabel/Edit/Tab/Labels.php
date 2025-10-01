<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit\Tab;

class Labels extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_productlabel;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magebees\Productlabel\Model\Productlabel $productlabel,
        array $data = []
    ) {
        $this->_productlabel = $productlabel;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('productlabel_data');
          
        $form = $this->_formFactory->create();

        /* category page */
        $fieldset = $form->addFieldset('category_page_label', ['legend' => __('Category Page Label')]);
        
        //for get image url
        $fieldset->addType('image', '\Magebees\Productlabel\Block\Adminhtml\Productlabel\Helper\Category');
            
        $fieldset->addField(
            'cat_position',
            'select',
            [
                'name' => 'cat_position',
                'label' => __('Position'),
                'title' => __('Position'),
                'values' => $this->_productlabel->getPosition(),
            ]
        );
        
        $fieldset->addField(
            'cat_image',
            'image',
            [
                'name' => 'cat_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
            ]
        );
        
        $fieldset->addField(
            'cat_image_width',
            'text',
            [
                'name' => 'cat_image_width',
                'label' => __('Image Width'),
                'title' => __('Image Width'),
                'required' => false,
                'class' => 'validate-digits',
                'placeholder' => '80',
            ]
        );
        
        $fieldset->addField(
            'cat_image_height',
            'text',
            [
                'name' => 'cat_image_height',
                'label' => __('Image Height'),
                'title' => __('Image Height'),
                'required' => false,
                'class' => 'validate-digits',
                'placeholder' => '80',
            ]
        );
        
        $fieldset->addField(
            'cat_text',
            'text',
            [
                'name' => 'cat_text',
                'label' => __('Text'),
                'title' => __('Text'),
                'required' => false,
                'note'  => __('Write text here for display on label.'),
            ]
        );
        
        $fieldset->addField(
            'cat_text_color',
            'text',
            [
                'name' => 'cat_text_color',
                'label' => __('Choose Text Color'),
                'title' => __('Choose Text Color'),
                'required' => false,
                'class' => 'color',
            ]
        );
        
        $fieldset->addField(
            'cat_text_size',
            'text',
            [
                'name' => 'cat_text_size',
                'label' => __('Text Font Size'),
                'title' => __('Text Font Size'),
                'required' => false,
                'class' => 'validate-digits',
                'note' => __('Add font size in pixels'),
            ]
        );
        
        /* product page */
        $fieldset = $form->addFieldset('product_page_label', ['legend' => __('Product Page Label')]);
        
        //for get image url
        $fieldset->addType('image', '\Magebees\Productlabel\Block\Adminhtml\Productlabel\Helper\Product');
        
        $fieldset->addField(
            'prod_position',
            'select',
            [
                'name' => 'prod_position',
                'label' => __('Position'),
                'title' => __('Position'),
                'values' => $this->_productlabel->getPosition(),
            ]
        );
        
        $fieldset->addField(
            'prod_image',
            'image',
            [
                'name' => 'prod_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
            ]
        );
        
        $fieldset->addField(
            'prod_image_width',
            'text',
            [
                'name' => 'prod_image_width',
                'label' => __('Image Width'),
                'title' => __('Image Width'),
                'class' => 'validate-digits',
                'required' => false,
                'placeholder' => '110',
            ]
        );
        
        $fieldset->addField(
            'prod_image_height',
            'text',
            [
                'name' => 'prod_image_height',
                'label' => __('Image Height'),
                'title' => __('Image Height'),
                'class' => 'validate-digits',
                'required' => false,
                'placeholder' => '110',
            ]
        );
        
        $fieldset->addField(
            'prod_text',
            'text',
            [
                'name' => 'prod_text',
                'label' => __('Text'),
                'title' => __('Text'),
                'required' => false,
                'note'  => __('Write text here for display on label.'),
            ]
        );
        
        $fieldset->addField(
            'prod_text_color',
            'text',
            [
                'name' => 'prod_text_color',
                'label' => __('Choose Text Color'),
                'title' => __('Choose Text Color'),
                'required' => false,
                'class' => 'color',
            ]
        );
        
        $fieldset->addField(
            'prod_text_size',
            'text',
            [
                'name' => 'prod_text_size',
                'label' => __('Text Font Size'),
                'title' => __('Text Font Size'),
                'required' => false,
                'class' => 'validate-digits',
                'note' => __('Add font size in pixels'),
            ]
        );
        
        $model_data = $model->getData();
    
        $form->setValues($model_data);
        $this->setForm($form);
        
        return parent::_prepareForm();
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
