<?php

declare(strict_types=1);

namespace Magebees\CacheWarmer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;

class QuerySource extends FormField
{
    protected function _isInheritCheckboxRequired(AbstractElement $element)
    {
        return false;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        if ($element->getScope() != Form::SCOPE_DEFAULT) {
            $element->setData('disabled', true);
        }

        return parent::_getElementHtml($element);
    }
}
