<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Ranges
 */
class ListLocations extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('location', ['label' => __('Locations')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Locations');
    }


}
