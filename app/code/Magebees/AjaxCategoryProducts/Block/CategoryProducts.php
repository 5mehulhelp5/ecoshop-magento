<?php
namespace Magebees\AjaxCategoryProducts\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class CategoryProducts extends \Magento\Catalog\Block\Product\AbstractProduct{    protected $_productCollectionFactory;	protected $categoryFactory;	protected $urlHelper;	protected $_stockFilter;	
    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;
        
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\CatalogInventory\Helper\Stock $stockFilter,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->urlHelper = $urlHelper;
		$this->_stockFilter = $stockFilter;
        parent::__construct($context, $data);
    }
    
    public function setConfigValues()
    {
        $this->setListPosition($this->getConfigValues('list_position'));
        $this->setCategories($this->getConfigValues('categories'));
        $this->setDefaultCategory($this->getConfigValues('default_category'));
        $this->setShowCategorySectionTitle($this->getConfigValues('show_category_section_title'));
        $this->setCategorySectionTitle($this->getConfigValues('category_section_title'));
        $this->setNumberOfProducts($this->getConfigValues('number_of_products'));
        $this->setLoadingText($this->getConfigValues('loading_text'));
        $this->setDoneText($this->getConfigValues('done_text'));
        $this->setLoadMoreText($this->getConfigValues('load_more_text'));
        $this->setSortBy($this->getConfigValues('sort_by'));
        $this->setSortOrder($this->getConfigValues('sort_order'));
    }
    
    public function setWidgetOptions()
    {
        $this->setListPosition($this->getWdListPosition());
        $this->setCategories($this->getWdCategories());
        $this->setDefaultCategory($this->getWdDefaultCategory());
        $this->setShowCategorySectionTitle($this->getWdShowCategorySectionTitle());
        $this->setCategorySectionTitle($this->getWdCategorySectionTitle());
        $this->setNumberOfProducts($this->getWdNumberOfProducts());
        $this->setLoadingText($this->getWdLoadingText());
        $this->setDoneText($this->getWdDoneText());
        $this->setLoadMoreText($this->getWdLoadMoreText());
		$this->setSortBy($this->getWdSortBy());
        $this->setSortOrder($this->getWdSortOrder());
    }
    
    public function _getProductCollection()
    {
		//get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        
        //get values of current limit. if not the param value then it will set to 1
        $limit = (int)$this->getNumberOfProducts();
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : $limit;
       
		$categoryId = $this->getDefaultCategoryId();
		
		$cat_ids = $this->getRequest()->getParam('cat_ids');
		if($cat_ids){
			$categoryId = $cat_ids;
		}
        $sortBy = $this->getSortBy();
        $sortOrder = $this->getSortOrder();
		$category = $this->categoryFactory->create()->load($categoryId);
		
		if($sortBy == 'random'){
			$_productCollection = $this->_productCollectionFactory->create()
				->addAttributeToSelect('*')
				->addCategoryFilter($category);
			$_productCollection->getSelect()->orderRand();

		}else{
			$_productCollection = $this->_productCollectionFactory->create()
				->addAttributeToSelect('*')
				->setOrder($sortBy, $sortOrder)
				->addCategoryFilter($category);
		}
		
		$_productCollection->setPageSize($pageSize);
        $_productCollection->setCurPage($page);
		$this->total_collection = $_productCollection->getSize();
		
        $this->_productCollection = $_productCollection;
        
        return $this->_productCollection;
    }
    
    public function getConfigCategories()
    {
        $categories = $this->getCategories();
        return explode(',', $categories);
    }
    
    public function isEnabled()
    {
        $enabled = (bool)$this->_scopeConfig->getValue('ajaxcatpro/general/enabled', ScopeInterface::SCOPE_STORE);
        return $enabled;
    }

    public function getConfigValues($field)
    {
        return $this->_scopeConfig->getValue('ajaxcatpro/general/'.$field, ScopeInterface::SCOPE_STORE);
    }
    
    public function getCatFirstElement()
    {
        $cat = $this->getCategories();
        $cat_arr = explode(",", $cat);
        return current($cat_arr);
    }
    
    public function getDefaultCategoryId()
    {
        $defaultcat = $this->getDefaultCategory();
        if ($defaultcat) {
            $paramValue = $defaultcat;
        } else {
            $paramValue = $this->getCatFirstElement();
        }
        return $paramValue;
    }
    
    public function getPagerHtml()
    {
		 $class_alias=str_shuffle('magebees.loadmore.pager');
         $pager = $this->getLayout()->createBlock(
             'Magento\Theme\Block\Html\Pager',
             $class_alias
         )
                        ->setUseContainer(true)
                        ->setShowAmounts(false)
                        ->setPageVarName('p')
                        ->setLimit($this->getNumberOfProducts())
                        ->setCollection($this->_productCollection);
                        
        $this->setChild('pager', $pager);
		return $pager->toHtml();
        //return $this->getChildHtml('pager');
    }
    
  

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if ($this->getType()=="Magebees\AjaxCategoryProducts\Block\Widget\CategoryProductsWidget\Interceptor" || $this->getType()=="Magebees\AjaxCategoryProducts\Block\Widget\CategoryProductsWidget") {
            $this->setWidgetOptions();
        } else {
            $this->setConfigValues();
        }
       
        return parent::_beforeToHtml();
    }

  
    
    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    
    public function getCategoriesCollection($categoryId)
    {
        $cat_collection = $this->categoryFactory->create()->load($categoryId);
        $cat_details = [];
        $cat_details['name'] = $cat_collection->getName();
        $cat_details['url'] = $cat_collection->getUrl();
        return $cat_details;
    }
    
    public function getUniqueKey()
    {
        $key = uniqid();
        return $key;
    }
}
