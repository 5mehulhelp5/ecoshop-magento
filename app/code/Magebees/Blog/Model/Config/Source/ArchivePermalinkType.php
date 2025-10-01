<?php
namespace Magebees\Blog\Model\Config\Source;
class ArchivePermalinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default: mystore.com/blog_route/archive_route/archive-identifier/')],
            ['value' => 'short', 'label' => __('Short: mystore.com/archive_route/archive-identifier/')],
	    ['value' => 'archiveseourl', 'label' => __('SEO: mystore.com/archive-identifier/')],
        ];

    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
