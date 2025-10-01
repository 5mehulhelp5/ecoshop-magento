<?php
namespace Magebees\Blog\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class BlogArchives extends \Magebees\Blog\Block\Sidebar\Archive implements BlockInterface {
    protected $_template = "widget/archive.phtml";
}

