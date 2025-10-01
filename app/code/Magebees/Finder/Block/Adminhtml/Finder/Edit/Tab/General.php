<?php
namespace Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
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
        
        $model = $this->_coreRegistry->registry('finder_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('finder_id', 'hidden', ['name' => 'finder_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
        
        $fieldset->addField(
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
            
        if (!$model->getId()) {
            $fieldset->addField(
                'number_of_dropdowns',
                'text',
                [
                    'name' => 'number_of_dropdowns',
                    'label' => __('Number of Dropdowns'),
                    'title' => __('Number of Dropdowns'),
                    'required' => true,
                ]
            );
        }
        
        $dropdown_style = $fieldset->addField(
            'dropdown_style',
            'select',
            [
                'name' => 'dropdown_style',
                'label' => __('Drop Down Style'),
                'title' => __('Drop Down Style'),
                'values' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
            ]
        );
        
        $no_of_columns = $fieldset->addField(
            'no_of_columns',
            'select',
            [
                'name' => 'no_of_columns',
                'label' => __('Columns'),
                'title' => __('Columns'),
                'values' => [
                    '2' => __('2'),
                    '3' => __('3'),
                    '4' => __('4'),
                    '5' => __('5'),
                ],
            ]
        );
        
        $model_data = $model->getData();
        $form->setValues($model_data);
        $this->setForm($form);
        
        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($no_of_columns->getHtmlId(), $no_of_columns->getName())
            ->addFieldMap($dropdown_style->getHtmlId(), $dropdown_style->getName())
            ->addFieldDependence(
                $no_of_columns->getName(),
                $dropdown_style->getName(),
                'horizontal'
            ));
        
        
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
