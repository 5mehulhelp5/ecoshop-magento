<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxsearch\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	 protected $_storeManager;
     protected $searchbox;
     protected $httpRequest;
     protected $dirReader;
     protected $productMetadata;
    public function __construct(
		 \Magento\Framework\App\Helper\Context $context,
        \Magebees\Ajaxsearch\Block\Searchbox $searchbox,
		  \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $httpRequest,
		  \Magento\Framework\Module\Dir\Reader $dirReader,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
    
        $this->searchbox = $searchbox;
        $this->httpRequest = $httpRequest;
		$this->_storeManager = $storeManager;
	 	$this->dirReader = $dirReader;
	  	$this->productMetadata = $productMetadata;        
		parent::__construct($context);
    }
	 public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
     
    public function getSelectedCategory()
    {
        $param=$this->httpRequest->getParams();
        if (isset($param['cat'])) {
            return $cat_id=$param['cat'];
        } else {
            if (isset($param['category'])) {
                return $cat_id=$param['category'];
            }
        }
    }
    public function getSearchType()
    {
        $config=$this->searchbox->getConfig();
        $search_type=$config['search_type'];
        return $search_type;
    }
     public function getConfig()
    {
        $config=$this->searchbox->getConfig();       
        return $config;
    }
    public function getMatchType()
    {
        $config=$this->searchbox->getConfig();
        $search_type=$config['exact_match'];
        return $search_type;
    }
    
    public function getLayoutSetting()
    {
        $layout_config=$this->searchbox->getLayoutConfig();
        return $layout_config;
    }
	 public function getGeneral($field,$store_id=0){		
        return $this->scopeConfig->getValue('ajaxsearch/layout_setting/'.$field,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$store_id);		
	}
		public function getStoreCode(){
		return $this->_storeManager->getStore()->getCode();
	}
	public function checkCssExist()
	{
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$storecode=$this->getStoreCode();
		$moduleviewDir=$this->dirReader->getModuleDir('view', 'Magebees_Ajaxsearch');
        $cssDir=$moduleviewDir.'/frontend/web/css/dynamic_search'.$storecode.'.css';
		  return file_exists($cssDir);
	}
}
