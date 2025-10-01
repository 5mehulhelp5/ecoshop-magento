<?php
namespace Magebees\Onepagecheckout\Model\System\Config\Source;
use Magento\Framework\Option\ArrayInterface;

class Numberofattachment implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            1	=> __('1'),
			2	=> __('2'),
			3	=> __('3'),
			4	=> __('4'),
			5	=> __('5')
        ];
    }
}