<?php
namespace Magebees\Blog\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class BlogCategories extends \Magebees\Blog\Block\Sidebar\Category implements BlockInterface {
    protected $_template = "widget/category.phtml";
}

