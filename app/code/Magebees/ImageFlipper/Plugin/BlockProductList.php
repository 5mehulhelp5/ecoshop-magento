<?php

namespace Magebees\ImageFlipper\Plugin;

class BlockProductList
{
   
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
	protected $_storeManager;
    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
    

        
        $config=$this->scopeConfig->getValue('imageflipper/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enable=$config['enable'];
        $result = $proceed($product);
        if ($enable) {
            $flipper_img_name=$product->getData('flipper_img');
            
			if (($flipper_img_name!="no_selection")&&($flipper_img_name!="productno_selection")&&(($flipper_img_name != null))) {
                $image_url=$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product'.$flipper_img_name;
                $result = $proceed($product);
                return $result .'<span id="magebees_fliper_img" style="display:none;">'.$image_url.'</span>';
            }
        }
            $result = $proceed($product);
            return $result;
    }
}
