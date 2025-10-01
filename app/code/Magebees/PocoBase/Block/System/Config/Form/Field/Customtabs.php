<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Field;

class Customtabs extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
   
   /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
		$this->addColumn('tab_title', ['label' => __('Title'),'style' => 'width:80px']);
        $this->addColumn('tab_content', ['label' => __('Content'),'style' => 'width:100px']);
        $this->addColumn('staticblock_id', ['label' => __('Static Blocks'),'style' => 'width:80px']);
		$this->addColumn('attribute_code', ['label' => __('Attribute'),'style' => 'width:50px']);
		$this->addColumn('sort_order', ['label' => __('Order')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Tab');
    }
}
