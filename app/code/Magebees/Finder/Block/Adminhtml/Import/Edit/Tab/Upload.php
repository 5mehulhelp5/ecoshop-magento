<?php
namespace Magebees\Finder\Block\Adminhtml\Import\Edit\Tab;

class Upload extends \Magento\Backend\Block\Widget\Form\Generic
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('fid');
        //$model = $this->_coreRegistry->registry('finder_data');
        $form = $this->_formFactory->create();
        
        $msg_fieldset = $form->addFieldset('finder_form_msg', ['legend'=>__('**Important Notes.')]);
             
        $msg_fieldset->addField(
            'note',
            'label',
            [
                'label'     => 'Note : ',
                'after_element_html' => '<h4 style="color:#df280a">Please make sure that your CSV file should contains all field values along with SKUs</h4>',
            ]
        );
        
        $fieldset = $form->addFieldset('upload_form', ['legend'=>__('Upload CSV file only')]);

                              
        $fieldset->addField(
            'filename',
            'file',
            [
                'label'     => __('Upload CSV'),
                'name'      => 'filename',
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'finder_id',
            'hidden',
            [
                'name'      => 'finder_id',
                'value'     => $id
            ]
        );
        
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
