<?php
namespace Magebees\Blog\Model\Config\Source;
class DisplayMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '0' => 'Recent Blog Posts',
            '1' => 'Featured Blog Posts'
        ];
    }
}
