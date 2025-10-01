<?php
namespace Magebees\Finder\Block\Adminhtml\ImportUniversal\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/importuniversal'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                    'onsubmit' => 'importUniversalData()',
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
