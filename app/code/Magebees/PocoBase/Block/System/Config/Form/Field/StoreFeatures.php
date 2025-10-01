<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Ranges
 */
class StoreFeatures extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('static_block_id', ['label' => __('Static Blocks')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Block Identifier');
    }


}
