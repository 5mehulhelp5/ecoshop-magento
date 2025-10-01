<?php

namespace Magebees\AjaxInfiniteScroll\Block;

class AjaxScroll extends \Magento\Framework\View\Element\Template
{
    protected $_config = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = [],
        $attr = null
    ) {
    
        $this->_objectManager = $objectManager;
        $this->_scopeConfig=$context->getScopeConfig();
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }
    
     /*public function isAjaxaddtocartEnabled()
    {
        return $this->_moduleManager->isEnabled('Magebees_Ajaxaddtocart');
    }*/
    
    public function getConfig()
    {
        return $this->_scopeConfig->getValue('ajaxinfinitescroll/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getImageUrl($image)
    {
        
        $image_url=$this->_storeManager->getStore()
               ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'magebees/ajaxinfinitescroll/'.$image;
               return $image_url;
    }
}
