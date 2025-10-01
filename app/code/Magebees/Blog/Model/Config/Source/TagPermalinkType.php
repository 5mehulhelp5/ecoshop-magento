<?php
namespace Magebees\Blog\Model\Config\Source;
class TagPermalinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default: mystore.com/blog_route/tag_route/tag-identifier/')],
            ['value' => 'short', 'label' => __('Short: mystore.com/tag_route/tag-identifier/')],			['value' => 'tagseourl', 'label' => __('SEO: mystore.com/tag-identifier/')],
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
