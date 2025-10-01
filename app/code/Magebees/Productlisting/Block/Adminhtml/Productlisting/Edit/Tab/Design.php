<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab;

class Design extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
       
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('productlisting_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Design Options')]);

    	$fieldset->addField(
            'back_image',
            'image',
            [
                'name' => 'back_image',
                'label' => __('Background'),
                'title' => __('Background'),
                'required' => false,
            ]
        );
		
		$fieldset->addField(
            'back_color',
            'text',
            [
                'name' => 'back_color',
                'label' => __('Background Color'),
                'title' => __('Background Color'),
                'required' => false,
                'class' => 'color',
            ]
        );

        $fieldset->addField(
            'spacing_section',
            'select',
            [
                'label'     => __('Spacing on Section'),
                'name'      => 'spacing_section',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
        
        $fieldset->addField(
            'bottom_spacing_section',
            'select',
            [
                'label'     => __('Bottom Spacing on Section'),
                'name'      => 'bottom_spacing_section',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
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
