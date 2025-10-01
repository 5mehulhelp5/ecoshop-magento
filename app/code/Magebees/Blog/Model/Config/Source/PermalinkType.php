<?php
namespace Magebees\Blog\Model\Config\Source;
class PermalinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default: mystore.com/blog_route/post_route/post-identifier/')],
            ['value' => 'short', 'label' => __('Short: mystore.com/blog_route/post-identifier/')],
            ['value' => 'postseourl', 'label' => __('SEO: mystore.com/post-identifier/')],
            
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
