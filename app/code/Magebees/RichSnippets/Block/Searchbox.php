<?php

namespace Magebees\RichSnippets\Block;

use Magento\Framework\View\Element\Template;

class Searchbox extends Template
{
   
    protected $_template = 'searchbox.phtml';
	protected $_scopeConfig;
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    public function canShowContent()
    {
        $ext_enable=$this->_scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $searchbox_enable=$this->_scopeConfig->getValue('richsnippets/searchbox/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
          $name_enable=$this->_scopeConfig->getValue('richsnippets/searchbox/enable_websitename',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
          $website_name=$this->_scopeConfig->getValue('richsnippets/searchbox/websitename',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         if ((!$searchbox_enable)||(!$ext_enable)) {
            return false;
        }
        $base_url=$this->_storeManager->getStore()->getBaseUrl();
        $websiteParameters = array(
            'sitename' => $website_name,
            'enable_sitename'=>$name_enable,
            'siteurl' =>$base_url 
        );
        if (array_filter($websiteParameters)) {
            return $websiteParameters;
        }

        return false;
    }
}
