<?php

namespace Magebees\Ajaxquickview\Plugin;

class AjaxCategoryProductsList
{
   
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetProductDetailsHtml(
        \Magebees\AjaxCategoryProducts\Block\CategoryProducts $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
    

        $config=$this->scopeConfig->getValue('ajaxquickview/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $popup_config=$this->scopeConfig->getValue('ajaxquickview/popupsetting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $button_text=$popup_config['button_text'];
        $button_color=$popup_config['button_color'];
        $button_text_color=$popup_config['button_text_color'];
        $enable=$config['enable'];
        $auto = $config['auto'];
        $result = $proceed($product);
        $pathbase = 'ajaxquickview/index/index';
        $base_url = str_replace("index.php/", "", $this->urlInterface->getUrl());
        $baseUrl = $base_url.$pathbase;
        $product_url=$product->getProductUrl();
        $producturlpath = str_replace($base_url, "", $product_url);
        $producturlpath = (preg_match('/index.php/', $producturlpath)) ? str_replace('index.php/', '', $producturlpath) : $producturlpath;
        $productUrl = $baseUrl."/path/".$producturlpath;
        if ($enable && $auto) {
            return $result . "<button class='magebees_quickview' title='Quick View' href='$productUrl'      style='background-color:$button_color'><span style='color:$button_text_color'>$button_text</span></button>";
        }
        
        return $result;
    }
}
