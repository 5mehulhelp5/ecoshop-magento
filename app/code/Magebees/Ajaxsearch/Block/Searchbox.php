<?php
namespace Magebees\Ajaxsearch\Block;

use Magento\Search\Model\QueryFactory;

class Searchbox extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magebees\Ajaxsearch\Model\Config\Categories $catSearchModel,
        \Magento\Search\Model\ResourceModel\Query\Collection $queryCollection,
		 \Magento\Search\Model\ResourceModel\Query\CollectionFactory $queriesFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\Url $urlBuilder,
         \Magento\Store\Model\StoreManagerInterface $storeManager,        
        \Magento\Framework\App\Request\Http $httpRequest,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        $this->catSearchModel = $catSearchModel;
        $this->queryCollection = $queryCollection;
		 $this->_queriesFactory = $queriesFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->pageFactory = $pageFactory;
        $this->urlBuilder = $urlBuilder;
        $this->httpRequest = $httpRequest;
        $this->storeManager = $storeManager;        
        parent::__construct($context, $data);
    }

    public function loadCategory($categoryId)
    {
        $category=$this->_categoryFactory->create()->load($categoryId);
        return $category;
    }
    
    public function loadProduct($productId)
    {
        $product=$this->productFactory->create()->load($productId);
        return $product;
    }
    public function limit_word($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }   
    
    public function getConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getRecentConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/recent_search_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getPopularConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/popular_search_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getSuggestedConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/suggested_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getCatFilterConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/cat_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getCatConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/category_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getCmsConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/cms_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getAttrConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/attributes');
    }
    public function getProductsConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/product_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getLayoutConfig()
    {
        return $this->_scopeConfig->getValue('ajaxsearch/layout_setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getConfigCateArray()
    {
        $main_array=$this->catSearchModel->toOptionArray();		$main_arr = array();
        foreach ($main_array as $arr) {
            if ($arr['value']!=0) {
                $main_arr[$arr['value']]=$arr['label'];
            }
        }
        return $main_arr;
    }
    public function getRecentSearchCollection()
    {
        $term=$this->getSearchTerm();
        $recent_config=$this->getRecentConfig();
        $recent_limit=$recent_config['limit'];
        $recent_collection=$this->queryCollection;
        $recent_collection->setRecentQueryFilter();
        $recent_collection->addFieldToFilter('query_text', [
        ['like' => '%'.$term.'%']
        ]);
        if ($recent_limit!=0) {
            $recent_collection->getSelect()->limit($recent_limit);
        } else {
            $recent_collection=[];
        }
        $data=$recent_collection->getData();
        return $data;
    }
    public function getRecentCollection()
    {
        $recent_config=$this->getRecentConfig();
        $recent_limit=$recent_config['limit'];
        $recent_collection= $this->_queriesFactory->create();
        $recent_collection->setRecentQueryFilter();
        if ($recent_limit!=0) {
            $recent_collection->getSelect()->limit($recent_limit);
        } else {
            $recent_collection=[];
        }
        
        return $recent_collection;
    }
    public function getPopularSearchCollection()
    {
        $term=$this->getSearchTerm();
        $popular_config=$this->getPopularConfig();
        $popular_limit=$popular_config['limit'];
        $popular_collection=$this->queryCollection;
        $popular_collection->setPopularQueryFilter();
        $popular_collection->addFieldToFilter('query_text', [
        ['like' => '%'.$term.'%']
        ]);
        if ($popular_limit!=0) {
            $popular_collection->getSelect()->limit($popular_limit);
        } else {
            $popular_collection=[];
        }
            
        return $popular_collection;
    }
    public function getCategorySearchCollection() 
    {
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId(); 
        $term=$this->getSearchTerm();
        $cat_config=$this->getCatConfig();
        $names=$this->_categoryFactory->create()->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))->addAttributeToFilter('is_active','1')->addFieldToFilter('name', [
        ['like' => '%'.$term.'%']
        ]);
        foreach ($names as $name) {
            $name_id[]=$name['entity_id'];
        }
        $description =$this->_categoryFactory->create()->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))->addAttributeToFilter('is_active','1')->addFieldToFilter('description', [
        ['like' => '%'.$term.'%']
        ]);
        foreach ($description as $desc) {
            $desc_id[]=$desc['entity_id'];
        }
        if (empty($desc_id)) {
            if (!empty($name_id)) {
                $cat_ids=$name_id;
            } else {
                $cat_ids=[];
            }
        } elseif (empty($name_id)) {
            if (!empty($desc_id)) {
                $cat_ids=$desc_id;
            } else {
                $cat_ids=[];
            }
        } else {
            $cat_ids=array_unique(array_merge($name_id, $desc_id));
        }
        $cat_limit=$cat_config['limit'];
        $new=array_slice($cat_ids, 0, $cat_limit, true);
        return $new;
    }
    public function getCmsSearchCollection()
    {
        $term=$this->getSearchTerm();
        $cms_config=$this->getCmsConfig();
        $page_limit=$cms_config['limit'];
        $pages =$this->pageFactory->create()->getCollection()->addStoreFilter($this->storeManager->getStore()->getId());
        $pages->addFieldToFilter(['title', 'content'], [
            ['like'=>'%'.$term.'%'],
            ['like'=>'%'.$term.'%'] ]);
        if ($page_limit!=0) {
            $pages->getSelect()->limit($page_limit);
        } else {
            $pages=[];
        }
                    
        return $pages;
    }
    public function getCatalogSearchUrl()
    {
        return $this->urlBuilder->getUrl('catalogsearch/result');
    }
    public function getSearchTerm()
    {
        $param=$this->httpRequest->getParams();
        $term=$param['q'];
        return $term;
    }
    public function getBaseUrl()
    {
        return $this->urlBuilder->getBaseUrl();
    }
}
