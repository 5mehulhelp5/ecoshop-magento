<?php
namespace Magebees\Blog\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class BlogTags extends \Magebees\Blog\Block\Sidebar\TagList implements BlockInterface {
    protected $_template = "widget/tags.phtml";
}

