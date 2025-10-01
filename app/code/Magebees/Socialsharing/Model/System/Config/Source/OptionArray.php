<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;
use Magento\Framework\Option\ArrayInterface;

abstract class OptionArray implements ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }
    abstract public function getOptionHash();
}