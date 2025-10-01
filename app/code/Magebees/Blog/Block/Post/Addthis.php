<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
class Addthis extends Template {
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magebees\Blog\Helper\Configuration $Configuration) {
        $this->configuration = $Configuration;
        parent::__construct($context);
    }
    public function EnableAddThis() {
        return $add_this_enable = $this->configuration->getConfig('blog/add_this/enabled');
    }
    public function getAddThisId() {
        return $add_this_id = $this->configuration->getConfig('blog/add_this/id');
    }
    public function getAddThisLanguage() {
        return $add_this_language = $this->configuration->getConfig('blog/add_this/language');
    }
}
