<?php
namespace Magebees\Finder\Block;

class FinderProduct extends \Magento\Catalog\Block\Product\AbstractProduct {
	protected $pager;
	protected $urlHelper;
    protected $_productCollectionFactory;
    protected $stockHelper;
	protected $_catalogSession;
	protected $urlInterfaceObj;
    protected $_finderHelper;
    protected $_finderFactory;
    protected $_ymmvalueFactory;
    protected $_mapvalueFactory;
    protected $universalProduct;	
	protected $_coreResource;	
	protected $_categoryFactory;
	
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
		\Magento\Catalog\Model\Session $catalogSession,
		\Magento\Framework\App\Request\Http $urlInterfaceObj,
		\Magebees\Finder\Helper\Data $finderHelper,
		\Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
		\Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory,
		\Magebees\Finder\Model\UniversalProduct $universalProduct,
		\Magebees\Finder\Model\FinderFactory $finderFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,	
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockHelper=$stockHelper;
		$this->_catalogSession = $catalogSession;
		$this->urlInterfaceObj = $urlInterfaceObj;
        $this->_finderHelper = $finderHelper;
        $this->_finderFactory = $finderFactory;
        $this->_ymmvalueFactory = $ymmvalueFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
        $this->universalProduct = $universalProduct;
		$this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }
	
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__($this->getPageTitle()));//set Page title
		return parent::_prepareLayout();
	}
	
	protected function _beforeToHtml()
    {
		$this->setProductCollection($this->getFinderProductCollection());
	}
	
	public function getLoadedProductCollection()
    {
		return $this->getProductCollection();
    }
	
	public function getProdcutSkusArr($element)
    {
        return $element['sku'];
    }
	/**
     * Get product collection
     */
	public function getFinderProductCollection()  {
		$urlstring = $this->urlInterfaceObj->getRequestString();
        $search = strpos($urlstring, 'finder');
        $reset = $this->_finderHelper->resetFinder($urlstring);
		if ($search) {
			if (!$reset) {
                $skus = ['0'=>''];
            } else {
				$finderId = $this->_finderHelper->getFinderId($urlstring);
                $finder = $this->_finderFactory->create()->load($finderId);
                $path = $finder->getYmmValueFromPath($urlstring);

                $current  = $finder->getSavedValue('current');
                if ($path!=$current) {
                    $dropdowns = $finder->getDropdownsByCurrent($path);
                    $finder->saveDropDownValues($dropdowns);
                }

                $skus = $product_skus = [];
                $last = $finder->getSavedValue('last');
                $product_string = "";
				
				if ($last) {
                    $product_string = $this->_mapvalueFactory->create()->load($last, 'ymm_value_id')->getSku();
                    $product_skus = explode("|", $product_string);
                } elseif ($finder->getSavedValue('current')) {
                    $this->getValues($finder->getSavedValue('current'));// for get sku if all dropdown not selected
                    foreach ($this->_parent_ids as $ymm_value_id) {
                             $new_skus = $this->_mapvalueFactory->create()->load($ymm_value_id, 'ymm_value_id')->getSku();
                        $new = explode("|", $new_skus);
                        $product_skus = array_merge($product_skus, $new);
                    }
                }
				$skus = $product_skus;
			//	print_r($skus);exit;
				//set sort orders
                $enable_universal = $this->_scopeConfig->getValue('finder/general/universal_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($enable_universal) {
                    $universal_collection = $this->universalProduct->getCollection()->addFieldToFilter('finder_id', $finderId);
                    $universal_products_skus = array_map([$this,"getProdcutSkusArr"], $universal_collection->getData());
                    if (!empty($universal_products_skus)) {
                        $skus = array_unique(array_merge($skus, $universal_products_skus));
                        $search_skus = array_diff($skus, $universal_products_skus);
                        if (!$this->urlInterfaceObj->getParam('product_list_order')) {
                        //Setting Sort order which sort based on the array elements order
                            $sort_order = $this->_scopeConfig->getValue('finder/general/sort_order', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($sort_order == 2) {
                               $this->getSelect()->order("find_in_set(e.sku,'".implode(',', $universal_products_skus)."')");//display universal products last
                            } elseif ($sort_order == 3) {
                                $this->getSelect()->order("find_in_set(e.sku,'".implode(',', $search_skus)."')");//display search products last
                            }
                        }
                    }
                }
			
			
			}
			$page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
			$storeId=$this->_storeManager->getStore()->getId();
			/** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
			$collection = $this->_productCollectionFactory->create();
			$collection = $this->_addProductAttributesAndPrices($collection)
				->setStore($storeId)
				->addStoreFilter($storeId)
				->addFieldToFilter('sku', array('in' => $skus))
				->addAttributeToFilter('visibility', 4)
				->setPageSize(10)
				->setCurPage($page);
			$this->stockHelper->addInStockFilterToCollection($collection);
			
			//Filter by category
			$catid=($this->getRequest()->getParam('cat'))? $this->getRequest()->getParam('cat') : 0;
			
			if($catid!=0){
				$category = $this->_categoryFactory->create()->load($catid);
				$collection->addCategoryFilter($category);
			}
		}
		return $collection;
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
            'product' => $product->getEntityId(),
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
	
	public function getFinderById($finderId)
    {
        return $this->_finderFactory->create()->load($finderId);
    }
	
	/* Get the configured title of section */
    public function getPageTitle() {
		$path = trim($this->getRequest()->getRequestString(),'/');
		$finderId = $this->_finderHelper->getFinderId($path);
		$finders = $this->getFinderById($finderId);
		$lasturlval = $finders->getYmmValueFromPath($path);
		$flag = true;
		$values = array();
		while($flag == true){
			$valueModel =  $this->_ymmvalueFactory->create()->load($lasturlval);
			$parent_id = $valueModel->getParentId();
			$values[] = $valueModel->getValue();
			if($parent_id == 0){
				$flag = false;
			} else {
				$lasturlval = $parent_id;
			}
		}
		
		if (!($values[0])){
            return "Search Result Page";    
        }
		
		$title = array_reverse($values);
		$title = implode(', ',$title);
		$res = $this->_scopeConfig->getValue('finder/general/finderpage_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $res." "."'".$title."'";
	} 
	
	public function getValues($parentId)
    {
        $collection = $this->_ymmvalueFactory->create()->getCollection()->addFieldToFilter('parent_id', $parentId);
        $data = $collection->getData();
        if (!$data) {
            $this->_parent_ids[] = $parentId;
        }
              
        foreach ($collection as $option) {
            $this->getValues($option->getYmmValueId());
        }
    }
	
	public function getPagerHtml() {
		$count = $this->getProductCollection()->getSize();
        if ($this->getProductCollection()->getSize()) {
            if (!$this->pager) {
                 $this->pager = $this->getLayout()->createBlock(
                     'Magento\Catalog\Block\Product\Widget\Html\Pager',
                     'finder.pager'
                 );

				$this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName('p')
                    ->setLimit(10)
                    ->setTotalLimit($count)
                    ->setCollection($this->getProductCollection());
            }

            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
	
	public function getToolbarHtml()
    {
        $toolbar = $this->getLayout()
                   ->createBlock(
                'Magebees\Finder\Block\ResultToolbar',
                'product_list_toolbar'
                )
                ->setTemplate('Magebees_Finder::toolbar.phtml')
                ->setCollection($this->getProductCollection());
		$this->setChild('toolbar', $toolbar);
		return $toolbar->toHtml();		
    }
	
	 public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }
}