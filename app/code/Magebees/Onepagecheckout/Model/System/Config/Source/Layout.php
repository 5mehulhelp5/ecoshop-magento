<?php 
namespace Magebees\Onepagecheckout\Model\System\Config\Source;
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
        		['value' =>'0','label' => __('2 Column Alternative Layout')],
        		['value' =>'1', 'label' => __('1 Columns')]
        	];
    }
}
