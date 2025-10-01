<?php

namespace Magebees\RichSnippets\Plugin;

class ListProduct
{
  
    protected $scopeConfig;
	protected $_request;

   
    public function __construct(
       \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        \Closure $proceed,         
        \Magento\Catalog\Model\Product $product
    ) {
   
        $ext_enable=$this->scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $cat_enable=$this->scopeConfig->getValue('richsnippets/category/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       $result = $proceed($product);  
       $layout = $subject->getLayout();
        $current_action=$this->_request->getFullActionName();
        if($ext_enable&&$cat_enable)
        {
        if($current_action=='catalog_category_view')
        {  
        
        if(!$layout->getBlock('magebees.richsnippet.product.plugin'))
        {
            $productBlock = $layout->createBlock('Magento\Framework\View\Element\Template', 'magebees.richsnippet.product.plugin');
        }
        else
        {
             $productBlock=$layout->getBlock('magebees.richsnippet.product.plugin');
        }
            $productBlock->setProduct($product);
            $productBlock->setTemplate('Magebees_RichSnippets::product/list/rich_snippets.phtml');
            $script=$productBlock->toHtml();      
            return $result.$script;
        }        
        }
        return $result;       
    }
}
