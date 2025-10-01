<?php
namespace Magebees\Ajaxaddtocart\Block;

class Popup extends \Magento\Framework\View\Element\Template
{
    protected $_config;
    protected $filterProvider;
    protected $pageModel;
     
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\Page $pageModel
    ) {
        parent::__construct($context);
        $this->filterProvider = $filterProvider;
        $this->pageModel = $pageModel;
    }
    
    public function getImageUrl($image)
    {
        
        $image_url=$this->_storeManager->getStore()
               ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'ajaxaddtocart/'.$image;
               return $image_url;
    }
    
     
     /* For get the configuration value of default extension settings*/
    public function getConfig()
    {
        return $this->_scopeConfig->getValue('ajaxaddtocart/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

     public function getConfigProductImageType() 
    {   
        return $this->_scopeConfig->getValue('checkout/cart/configurable_product_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  
                
    }   
   
    
    /* For get the configuration value of dialogbox settings*/
    public function getDialogConfig()
    {
        return $this->_scopeConfig->getValue('ajaxaddtocart/dialogboxsetting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /* For get the configuration value for confirmation content after add to cart */
    public function getConfirmContent()
    {
        $confirmConfig=$this->_scopeConfig->getValue('ajaxaddtocart/confirmaddtocart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $content=$confirmConfig['addblock'];
        $html = $this->filterProvider->getBlockFilter()->filter($content);
        return $html;
    }
     
     /* For get the configuration value for confirmation content after remove product from cart */
    public function getRemoveConfirmContent()
    {
        $confirmConfig=$this->_scopeConfig->getValue('ajaxaddtocart/confirmremovecart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $content=$confirmConfig['removeblock'];
        $html =$this->filterProvider->getBlockFilter()->filter($content);
        return $html;
    }
     
     /* For add loader image template and loader css*/
    public function manageLoader()
    {
        $this->setTemplate('loader.phtml');
    }
    public function identifyAction()
    {
        if ($this->_request->getFullActionName()=='catalog_product_view') {
            return 1;
        } else {
            return 0;
        }
    }
    
    /* For manage script template and condition for enable script */
    public function manageScriptTemplate()
    {
        $config=$this->getConfig();
        $routeName =$this->_request->getRouteName();
        $identifier=$this->pageModel->getIdentifier();
        if ($config['enable']==1) {
            if (($this->_request->getFullActionName() != 'wishlist_index_configure') && ($this->_request->getFullActionName()!='catalog_product_view')) {
                if ($routeName == 'cms' && $identifier == 'home') {
                    $home_enable=$config['enable_home'];
                    if ($home_enable==1) {
                        $this->setTemplate('script.phtml');
                    } else {
                        $this->setTemplate('home_script.phtml');
                    }
                } else {
                    $this->setTemplate('script.phtml');
                }
            } else {
                 $this->setTemplate('ajaxsearch_script.phtml');
            }
        } else {
            if ($routeName == 'cms' && $identifier == 'home') {
                $this->setTemplate('home_script.phtml');
            }
        }
    }
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
