<?php

namespace Magebees\Advertisementblock\Block\Adminhtml\Advertisementblock;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'advertisement_id';
        $this->_controller = 'adminhtml_advertisementblock';
        $this->_blockGroup = 'Magebees_Advertisementblock';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );
        $AdvertisementblockId=(int) $this->getRequest()->getParam('id');
        if ($AdvertisementblockId) {
            $this->addButton(
                'delete',
                [
                     'label' => __('Delete'),
                     'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to delete this?'))
                     . ','
                     . json_encode($this->getDeleteUrl())
                     . ')',
                     'class' => 'scalable delete',
                     'level' => -1
                     ]
            );
        }
    }
     /**
      * Prepare layout
      *
      * @return \Magento\Framework\View\Element\AbstractBlock
      */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
    
    public function getDeleteUrl(array $args = [])
    {
        $id=(int) $this->getRequest()->getParam('id');
 
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
