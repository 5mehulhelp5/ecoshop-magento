<?php
namespace Magebees\Blog\Model\Config\Source;
class AuthorPermalinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default: mystore.com/blog_route/author_route/author-identifier/')],
            ['value' => 'short', 'label' => __('Short: mystore.com/author_route/author-identifier/')],
	    ['value' => 'authorseourl', 'label' => __('SEO: mystore.com/author-identifier/')],
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
