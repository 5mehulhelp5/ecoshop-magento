<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Ranges
 */
class NotificationMessage extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('message', ['label' => __('Notification Message')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Notification Message');
    }


}
