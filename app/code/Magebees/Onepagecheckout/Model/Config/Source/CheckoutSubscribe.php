<?php
namespace Magebees\Onepagecheckout\Model\Config\Source;
class CheckoutSubscribe implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Checked by default')],
            ['value' => 2, 'label' => __('Not Checked by default')],
            ['value' => 3, 'label' => __('Force subscription not showing')],
            ['value' => 4, 'label' => __('Force subscription')]
        ];
    }
}
