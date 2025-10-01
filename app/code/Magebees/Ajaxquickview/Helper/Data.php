<?php
namespace Magebees\Ajaxquickview\Helper;
/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $productMetadata;
	protected $compareHelper;
	protected $postHelper;
   
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,             
		\Magento\Catalog\Helper\Product\Compare $compareHelper,
         \Magento\Framework\Data\Helper\PostHelper $postHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {        
        $this->productMetadata = $productMetadata;  
        $this->compareHelper = $compareHelper;  
         $this->postHelper = $postHelper;
        parent::__construct($context);
    }
    
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
    public function getConfigValues(){
        return $this->scopeConfig;
    }
    
    public function getQuickViewUrl($product){
        $pathbase = 'ajaxquickview/index/index';
        $base_url = str_replace("index.php/", "", $this->_urlBuilder->getUrl());
        $baseUrl = $base_url.$pathbase;
        $product_url=$product->getProductUrl();
        $producturlpath = str_replace($base_url, "", $product_url);
        $producturlpath = (preg_match('/index.php/', $producturlpath)) ? str_replace('index.php/', '', $producturlpath) : $producturlpath;
        $productUrl = $baseUrl."/path/".$producturlpath;
        return $productUrl;
    }

    public function addQuickViewButton($product)
    {
        $config=$this->scopeConfig->getValue('ajaxquickview/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $popup_config=$this->scopeConfig->getValue('ajaxquickview/popupsetting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $button_text=$popup_config['button_text'];
        $button_color=$popup_config['button_color'];
        $button_text_color=$popup_config['button_text_color'];
        $enable=$config['enable'];        
        
        $productUrl = $this->getQuickViewUrl($product);
        if ($enable) {
            return "<button class='magebees_quickview' title='$button_text' href='$productUrl' style='background-color:$button_color'>
                        <span style='color:$button_text_color'>$button_text</span>
                    </button>";
        }
        
    }
   
    public function getPostDataParams($product)
    {
         $params = ['product' => $product->getId()];
        $params[\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = "";
        return $this->postHelper->getPostData($this->compareHelper->getAddUrl(), $params);
    }
    public function getSimpleTemplate(){
        if($this->scopeConfig->getValue('customstockstatus/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->scopeConfig->getValue('customstockstatus/display_settings/other_view', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $template = 'Magebees_Customstockstatus::product/view/type/default.phtml';
        }else{
            $template = 'Magento_Catalog::product/view/type/default.phtml';
        } 
        return $template;
    }
    public function getGroupedTemplate(){
        if($this->scopeConfig->getValue('customstockstatus/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->scopeConfig->getValue('customstockstatus/display_settings/other_view', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $template = 'Magebees_Customstockstatus::product/view/default.phtml';
        }else{
            $template = 'Magento_GroupedProduct::product/view/type/default.phtml';
        } 
        return $template;
    }

    public function getGroupedProductTemplate(){
        if($this->scopeConfig->getValue('customstockstatus/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->scopeConfig->getValue('customstockstatus/display_settings/other_view', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $template = 'Magebees_Customstockstatus::product/view/type/grouped.phtml';
        }else{
            $template = 'Magento_GroupedProduct::product/view/type/grouped.phtml';
        } 
        return $template;
    }
    public function getBundleTemplate()
    {
     if($this->scopeConfig->getValue('customstockstatus/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->scopeConfig->getValue('customstockstatus/display_settings/other_view', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $template = 'Magebees_Customstockstatus::catalog/product/view/type/bundle.phtml';
        }else{
            $template = 'Magento_Bundle::catalog/product/view/type/bundle.phtml';
        } 
        return $template;   
    }
    public function getConfigurableTemplate()
    {
     if($this->scopeConfig->getValue('customstockstatus/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->scopeConfig->getValue('customstockstatus/display_settings/other_view', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $template = 'Magebees_Ajaxquickview::product/view/type/configurable.phtml';
        }else{
            $template = 'Magento_Catalog::product/view/type/default.phtml';
        } 
        return $template;   
       
    }
}
