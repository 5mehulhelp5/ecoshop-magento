<?php
namespace Magebees\Blog\Model\Config\Source;
class SortBy implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '0' => 'Publish Date (default)',
            '1' => 'Position',
	    '2' => 'Title',
	    '3' => 'Random'
        ];
    }
}
