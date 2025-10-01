<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class SocialMedia extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Name'),'style' => 'width:100px']);
        $this->addColumn('url', ['label' => __('URL'),'style' => 'width:150px']);
        $this->addColumn('font_icon', ['label' => __('Font Icon'),'style' => 'width:100px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }
}