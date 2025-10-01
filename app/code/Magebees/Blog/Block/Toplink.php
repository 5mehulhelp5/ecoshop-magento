<?php
namespace Magebees\Blog\Block;
use Magento\Store\Model\ScopeInterface;
class Toplink extends \Magento\Framework\View\Element\Html\Link {
    /**
     * @return string
     */
    public function getHref() {
        $url_key = $this->_scopeConfig->getValue('blog/permalink/route', ScopeInterface::SCOPE_STORE);
        return $this->getUrl($url_key, ['_secure' => true]);
    }
    public function getLabel() {
        return $this->_scopeConfig->getValue('blog/blogpage/title', ScopeInterface::SCOPE_STORE);
    }
    public function isEnable() {
		return $this->_scopeConfig->getValue('blog/general/module_enable_disable', ScopeInterface::SCOPE_STORE);

    }
}
