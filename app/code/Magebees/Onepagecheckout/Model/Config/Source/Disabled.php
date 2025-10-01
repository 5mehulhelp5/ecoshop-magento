<?php
namespace Magebees\Onepagecheckout\Model\Config\Source;

class Disabled implements \Magento\Framework\Option\ArrayInterface
{
    protected $localeLists;

    public function __construct(\Magento\Framework\Locale\ListsInterface $localeLists)
    {
        $this->localeLists = $localeLists;
    }
    public function toOptionArray()
    {
        $options = $this->localeLists->getOptionWeekdays();
        array_unshift($options, [
            'label' => __('No Day'),
            'value' => -1
        ]);
        return $options;
    }
}