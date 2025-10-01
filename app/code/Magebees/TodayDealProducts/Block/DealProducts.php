<?php
namespace Magebees\TodayDealProducts\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * Catalog Products List widget block
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DealProducts extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Name of request parameter for page number value
     *
     * @deprecated
     */
    const PAGE_VAR_NAME = 'dp';

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $pager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_stockFilter;
    protected $_dealFactory;
    protected $_dealHelper;
    protected $_dealData;
    protected $_dealProductIds = [];
    protected $_layoutoptions = [];
    protected $_count_timer = [];
    protected $_wd_today_deal_id = 0;
    

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magebees\TodayDealProducts\Model\DealFactory $dealFactory,
        \Magebees\TodayDealProducts\Helper\Data $dealHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,

                array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_stockFilter = $stockFilter;
        $this->httpContext = $httpContext;
        $this->_dealFactory = $dealFactory;
        $this->_dealHelper = $dealHelper;
        $this->_objectManager = $objectManager;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);
    }

   public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $argument = []
    ) {
        if (!isset($argument['zone'])) {
            $argument['zone'] = $renderZone;
        }
        $argument['price_id'] = isset($argument['price_id'])
            ? $argument['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $argument['include_container'] = isset($argument['include_container'])
            ? $argument['include_container']
            : true;
        $argument['display_minimal_price'] = isset($argument['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $argument
            );
        }
        return $price;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->_wd_today_deal_id = $this->getData('wd_deal')?$this->getData('wd_deal'):0;
        $this->setProductCollection($this->createCollection());
        if ($this->getType()=="Magebees\TodayDealProducts\Block\Widget\DealProductsWidget\Interceptor" || $this->getType()=="Magebees\TodayDealProducts\Block\Widget\DealProductsWidget") {
            $this->setTemplate($this->getTemplate());
        } else {
            if (isset($this->getDealData('layoutoptions')['enable_slider']) && $this->getDealData('layoutoptions')['enable_slider']) {
                $this->setTemplate('today_deal_products_slider.phtml');
            }
        }
        return parent::_beforeToHtml();
    }

    /*public function _toHtml()
    {
        if ($this->getType()=="Magebees\TodayDealProducts\Block\Widget\DealProductsWidget\Interceptor" || $this->getType()=="Magebees\TodayDealProducts\Block\Widget\DealProductsWidget") {
            $this->setTemplate($this->getTemplate());
        } else {
            if (isset($this->getDealData('layoutoptions')['enable_slider']) && $this->getDealData('layoutoptions')['enable_slider']) {
              	$this->setTemplate('today_deal_products_slider.phtml');
            }
        }
        return parent::_toHtml();
    }*/
    
    
    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        $page=($this->getRequest()->getParam('dp'))? $this->getRequest()->getParam('dp') : 1;
        $this->getDealProductsCollection();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        
		//$collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
    
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addFieldToFilter('entity_id', ['in' =>$this->_dealProductIds])
            ->setPageSize($this->getPageSize())
            ->setCurPage($page);
        //Display out of stock products
        if (!$this->getOutOfStock()) {
            $this->_stockFilter->addInStockFilterToCollection($collection);
        }
        return $collection;
    }
  
    public function getDealProductsCollection()
    {
        $storeId = $this->_dealHelper->getCurrentStoreId();
        $groupId = $this->_dealHelper->getCustomerGroupId();
        if ($this->_wd_today_deal_id > 0) {
            $today_deal_id = $this->_wd_today_deal_id;
        } elseif ($this->getUniqueCode()) {
            $today_deal_id = $this->_dealFactory->create()->load($this->getUniqueCode(), 'unique_code')->getId();
        } else {
            $today_deal_id = $this->getTodayDealId();
        }

        $now = $this->_dealHelper->getCurrentDate();
        $this->_dealData = $this->_dealFactory->create()->getCollection()
                            ->addFieldToFilter('today_deal_id', $today_deal_id)
                            ->addFieldToFilter('is_active', 1)
                            ->addFieldToFilter('from_date', ['lt' => $now])
                            ->addFieldToFilter('to_date', ['gt' => $now])
                            ->addFieldToFilter('customer_group_ids', [['finset' => $groupId]]);
        if (!$this->_dealHelper->isSingleStoreMode()) {
            $this->_dealData->addFieldToFilter('stores', [['finset' => $storeId]]);
        }
       
        if ($this->_dealData->getSize()) {
            $cond = null;
            $cond = $this->_dealData->getFirstItem()->getData('cond_serialize');
            
            $ruleModel = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Rule');
            $ruleModel->setConditions([]);
            $ruleModel->setConditionsSerialized($cond);
            $product_ids = [];
            $product_ids = $ruleModel->getMatchingProductIds();
            $this->_dealProductIds = array_keys($product_ids);
            $layoutoptions = null;
            $layoutoptions = $this->_dealData->getFirstItem()->getData('layoutoptions');
            $layoutoptions_array = [];
            $layoutoptions_array = json_decode($layoutoptions, true);
            $this->_dealData->getFirstItem()->setData('layoutoptions', $layoutoptions_array);
            $this->setDealData($this->_dealData->getFirstItem()->getData());
			$this->setLayoutValues();
        }
        
    }
    
    public function getConfigValues($field)
    {
        return $this->_scopeConfig->getValue('todaydealpro/general/'.$field, ScopeInterface::SCOPE_STORE);
    }
    
    public function setLayoutValues()
    {
        $price = $this->getDealData('layoutoptions')['price'];
        $this->setPrice($price);
             
        $cart = $this->getDealData('layoutoptions')['cart'];
        $this->setCart($cart);
        
        $compare = $this->getDealData('layoutoptions')['compare'];
        $this->setCompare($compare);
        
        $wishlist = $this->getDealData('layoutoptions')['wishlist'];
        $this->setWishlist($wishlist);
        
        $out_of_stock = $this->getDealData('layoutoptions')['out_of_stock'];
        $this->setOutOfStock($out_of_stock);
        
       	if(isset($this->getDealData('layoutoptions')['auto_scroll'])){
        	$auto_scroll = $this->getDealData('layoutoptions')['auto_scroll'];
        	$this->setAutoScroll($auto_scroll);
		}
		if(isset($this->getDealData('layoutoptions')['nav_arrow'])){
			$nav_arrow = $this->getDealData('layoutoptions')['nav_arrow'];
			$this->setNavArrow($nav_arrow);
		}
        
        $this->_count_timer['to_time'] = strtotime($this->getDealData('to_date'));
        $this->_count_timer['from_time'] = strtotime($this->getDealData('from_date'));
        $this->_count_timer['current_time'] = $this->getCurrentTime();
        $this->_count_timer['time_format'] = $this->getDealData('timer_format')?1:2;
        $this->setCountTimer($this->_count_timer);
    }
    
    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
		$total_products = 0;
		if(isset($this->getDealData('layoutoptions')['total_products'])){
			$total_products = $this->getDealData('layoutoptions')['total_products'];
		}
        return $total_products;
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        $products_per_page = $this->getDealData('layoutoptions')['products_per_page']?$this->getDealData('layoutoptions')['products_per_page']:self::DEFAULT_PRODUCTS_PER_PAGE;
        return $products_per_page;
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if ($this->getTemplate() == "today_deal_products_slider.phtml") {
            return false;
        }
		
		if(isset($this->getDealData('layoutoptions')['pager'])){
			$pager = (bool)$this->getDealData('layoutoptions')['pager'];
			return $pager;
		}else{
			return false;
		}
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        if (isset($this->getDealData('layoutoptions')['enable_slider']) && $this->getDealData('layoutoptions')['enable_slider']) {
            return $this->getProductsCount();
        }
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
		//echo $this->getProductCollection()->getSize();exit;
        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    'deal.products.list.pager'.$this->getDealData('today_deal_id')
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName('dp')
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
    
    public function getCurrentTime()
    {
        $currentDate = strtotime($this->_dealHelper->getCurrentDate());
        return $currentDate;
    }
    
    public function getUniqueSliderKey()
    {
        $key = uniqid();
        return $key;
    }
}