<?php
namespace Magebees\Productlisting\Block;

use Magento\Store\Model\ScopeInterface;

class Productlisting extends \Magento\Catalog\Block\Product\AbstractProduct
{
	protected $_productCollectionFactory;
    protected $_productCollection;
	protected $pager;
	protected $page_param;
	protected $_key;
	public $_listing_id;	
	public $_product_ids;
	public $sections_spacing;
	public $sections_bottom_spacing;
	public $bgcolor;
	public $bgimage;
	public $view_more_position;
	public $pagination_class;
	
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\CatalogInventory\Helper\Stock $stockFilter,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportModel,
		\Magebees\Productlisting\Model\ResourceModel\Product\Collection $bsautoCollection,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $config_product,
		\Magento\Bundle\Model\Product\Type $bundle_product,
		\Magebees\Productlisting\Model\ProductlistingFactory $listingFactory,
		\Magebees\Productlisting\Model\SelectProduct $selectProduct,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->urlHelper = $urlHelper;
		$this->_stockFilter = $stockFilter;
		$this->_coreResource = $resource;
		$this->reportModel = $reportModel;
		$this->_bsautoCollection = $bsautoCollection;
		$this->config_product=$config_product;
        $this->bundle_product=$bundle_product;
		$this->_listingFactory  = $listingFactory;
		$this->_manualCollection  = $selectProduct->getCollection();
		$this->scopeConfig = $scopeConfig;
		$this->_listing_data  = array();
		$this->_listing_id  = 0;
		$this->sections_spacing  = null;
		$this->sections_bottom_spacing  = null;
		$this->view_more_position  = null;
		
        parent::__construct($context, $data);
	}
	
	public function getConfigValue($value = '') {
		return $this->scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getMediaUrl(){
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
	
	public function getUniqueKey()
    {
        $key = uniqid();
		return $key;
        
    }
	
		
	protected function _beforeToHtml()
    {	
		$this->load_ajax = $this->getData('wd_load_ajax');
		
        $param_list_id = $this->getRequest()->getParam('listing_id'); 
		if($param_list_id){
			$this->_listing_id = $param_list_id;// view more page
			$this->sections_spacing = $this->getRequest()->getParam('wd_spacing')?$this->getRequest()->getParam('wd_spacing'):null;
			$this->sections_bottom_spacing = $this->getRequest()->getParam('wd_bottom_spacing')?$this->getRequest()->getParam('wd_bottom_spacing'):null;
			$this->bgcolor = $this->getRequest()->getParam('wd_bgcolor')?$this->getRequest()->getParam('wd_bgcolor'):null;
			$this->bgimage = $this->getRequest()->getParam('wd_bgimage')?$this->getRequest()->getParam('wd_bgimage'):null;
			$this->view_more_position  = $this->getRequest()->getParam('wd_view_more_position')?$this->getRequest()->getParam('wd_view_more_position'):'bottom';
	
		}else{
			$this->_wd_listing_id = $this->getData('wd_list')?$this->getData('wd_list'):0;
			//get widget value for load product usiign ajax or not
			$this->sections_spacing = $this->getData('wd_spacing')?$this->getData('wd_spacing'):null;
			$this->sections_bottom_spacing = $this->getData('wd_bottom_spacing')?$this->getData('wd_bottom_spacing'):null;
			$this->bgcolor = $this->getData('wd_bgcolor')?$this->getData('wd_bgcolor'):null;
			$this->bgimage = $this->getData('wd_bgimage')?$this->getData('wd_bgimage'):null;
			$this->view_more_position  = $this->getData('wd_view_more_position')?$this->getData('wd_view_more_position'):'bottom';
			
			if($this->load_ajax){
				$page= $this->getRequest()->getParam('list'.$this->_wd_listing_id);
				if($page > 1){
					$this->setTemplate('Magebees_Productlisting::product_listing.phtml');
				}else{
					$this->setTemplate('Magebees_Productlisting::load_product_list.phtml');
				}
			}else{
				$this->setTemplate('Magebees_Productlisting::product_listing.phtml');
			}
			
			//$this->setTemplate('product_listing.phtml');
			//comment due to call using ajax. uncomment if not use Ajax call
		}
		
		return parent::_beforeToHtml();

    }
	
	public function getTemplateFromId($listing_id){
		$this->_listing_data = $this->_listingFactory->create()->load($listing_id);
		$general = json_decode($this->_listing_data['general'],true);
		return $general['template'];
	}
	
	public function getListingCollection(){
		if($this->_listing_id == 0){
			if ($this->_wd_listing_id > 0) {
				$this->_listing_id = $this->_wd_listing_id;
			} else {
				$this->_listing_id = $this->getData('listing_id');
			}
		}
		//$this->_listing_id = $listing_id;
		$this->_listing_data = $this->_listingFactory->create()->load($this->_listing_id);
		$data = $this->_listing_data->getData();
		if($data){
		$this->general = json_decode($this->_listing_data['general'],true);
		$this->slider_options = json_decode($this->_listing_data['slider_options'],true);
		$this->display_settings = json_decode($this->_listing_data['display_settings'],true);
		$this->random_number = $this->getUniqueKey();
		$this->getProductCollectionByType();	
		}
		
	}
	public function getSwiperinitdata($listing_id)
{
    $swiper_data_init = [];

    $this->_listing_data = $this->_listingFactory->create()->load($listing_id);
    $this->general = json_decode($this->_listing_data['general'], true);
    $this->slider_options = json_decode($this->_listing_data['slider_options'], true);
    $this->display_settings = json_decode($this->_listing_data['display_settings'], true);

    $template = $this->general['template'];
    $slider = $this->slider_options['enable_slider'] ?? 0;
    $slider_id = 'productListing-' . $this->random_number;

    $autoscroll = isset($this->slider_options['autoplay']) && $this->slider_options['autoplay'] == '1';
    $custom_class = $this->display_settings['custom_class'] ?? null;
    $num_of_prod = $this->slider_options['num_of_prod'] ?? null;
    $items_per_page = $this->slider_options['items_per_page'] ?? null;
    $autoload = isset($this->slider_options['autoscroll']) && $this->slider_options['autoscroll'] == '1';
    $navarrow = isset($this->slider_options['nav_arr']) && $this->slider_options['nav_arr'] == '1';
    $pagination = isset($this->slider_options['pagination']) && $this->slider_options['pagination'] == '1';
    $paginationtype = $this->slider_options['pagi_type'] ?? 0;
    $infinite_loop = isset($this->slider_options['loop']) && $this->slider_options['loop'] == '1';
    $scrollbar = isset($this->slider_options['scrollbar']) && $this->slider_options['scrollbar'] == '1';
    $grap_cursor = isset($this->slider_options['grab_cur']) && $this->slider_options['grab_cur'] == '1';
    $grid = isset($this->slider_options['grid']) && $this->slider_options['grid'] == '1';
    $auto_height = isset($this->slider_options['auto_height']) && $this->slider_options['auto_height'] == '1';
    $items = $this->slider_options['items_per_slide'] ?? 4;
    $auto_play_dealytime = $this->slider_options['delay_time'] ?? 3000;
    $autoplayoff = isset($this->slider_options['mouse_enter']) && $this->slider_options['mouse_enter'] == '1';

    if ($slider && $template != "sidebar") {
        $swiper_data_init['slider_id'] = $slider_id;
        $swiper_data_init['autoHeight'] = $auto_height;
        $swiper_data_init['slidesPerView'] = 1;
        $swiper_data_init['spaceBetween'] = 10;
        if ($infinite_loop) {
            $swiper_data_init['loop'] = true;
        }

        if ($grap_cursor) {
            $swiper_data_init['grabCursor'] = true;
            $swiper_data_init['allowTouchMove'] = true;
        } else {
            $swiper_data_init['grabCursor'] = false;
            $swiper_data_init['allowTouchMove'] = false;
        }

        $swiper_data_init['a11y'] = false;

        if ($grid) {
            $swiper_data_init['grid']['rows'] = 2;
        }

        $swiper_data_init['lazy']['loadPrevNext'] = true;
        $swiper_data_init['lazy']['loadPrevNextAmount'] = 5;

        if ($autoscroll) {
            $swiper_data_init['autoplay']['delay'] = $auto_play_dealytime;
            $swiper_data_init['autoplay']['disableOnInteraction'] = false;
            $swiper_data_init['autoplay']['pauseOnMouseEnter'] = $autoplayoff;
        }

        if ($scrollbar) {
            $swiper_data_init['scrollbar']['el'] = "#swiper-scrollbar-" . $this->random_number;
            $swiper_data_init['scrollbar']['hide'] = false;
        }

        if ($navarrow) {
            $swiper_data_init['navigation']['nextEl'] = "#swiper-button-next-" . $this->random_number;
            $swiper_data_init['navigation']['prevEl'] = "#swiper-button-prev-" . $this->random_number;
        }

        if (in_array($paginationtype, ['default', 'dynamic', 'progress', 'fraction', 'custom'])) {
            $swiper_data_init['pagination_id'] = "#swiper-pagination-" . $this->random_number;
            $swiper_data_init['pagination_type'] = $paginationtype;
        }

        if ($custom_class == 'swiper_items5' && $items > 5) {
            $swiper_data_init['breakpoints'] = [
                '0' => ['slidesPerView' => 1, 'spaceBetween' => 20],
                '480' => ['slidesPerView' => 2, 'spaceBetween' => 20],
                '639' => ['slidesPerView' => 2, 'spaceBetween' => 20],
                '768' => ['slidesPerView' => 3, 'spaceBetween' => 30],
                '1200' => ['slidesPerView' => $items - 2, 'spaceBetween' => 30],
                '1440' => ['slidesPerView' => $items - 1, 'spaceBetween' => 30],
                '1920' => ['slidesPerView' => $items, 'spaceBetween' => 30]
            ];
        } elseif ($custom_class == 'swiper_items1') {
			$swiper_data_init['breakpoints'] = [
                '0' => ['slidesPerView' => 1, 'spaceBetween' => 10],
                '480' => ['slidesPerView' => 1, 'spaceBetween' => 10],
                '639' => ['slidesPerView' => 1, 'spaceBetween' => 10],
                '768' => ['slidesPerView' => 1, 'spaceBetween' => 10],
                '1200' => ['slidesPerView' => 1, 'spaceBetween' => 10]
            ];
		} else {
			$swiper_data_init['breakpoints'] = [
                '0' => ['slidesPerView' => 1, 'spaceBetween' => 20],
                '480' => ['slidesPerView' => 2, 'spaceBetween' => 20],
                '639' => ['slidesPerView' => 2, 'spaceBetween' => 20],
                '768' => ['slidesPerView' => 3, 'spaceBetween' => 30],
                '1200' => ['slidesPerView' => $items, 'spaceBetween' => 30]
            ];
        }
		
		

        if ($grid) {
            foreach ($swiper_data_init['breakpoints'] as $key => $breakpoint) {
                $swiper_data_init['breakpoints'][$key]['grid']['rows'] = 2;
                $swiper_data_init['breakpoints'][$key]['grid']['fill'] = 'row';
            }
        }
    } elseif ($slider && $template == "sidebar") {
        $swiper_data_init['slider_id'] = $slider_id;
        $swiper_data_init['autoHeight'] = $auto_height;
        $swiper_data_init['slidesPerView'] = 1;
        $swiper_data_init['spaceBetween'] = 30;

        if ($infinite_loop) {
            $swiper_data_init['loop'] = true;
        }

        if ($grap_cursor) {
            $swiper_data_init['grabCursor'] = true;
            $swiper_data_init['allowTouchMove'] = true;
        } else {
            $swiper_data_init['grabCursor'] = false;
            $swiper_data_init['allowTouchMove'] = false;
        }

        $swiper_data_init['a11y'] = false;

        if ($grid) {
            $swiper_data_init['grid']['rows'] = $items;
        }

        $swiper_data_init['lazy']['loadPrevNext'] = true;
        $swiper_data_init['lazy']['loadPrevNextAmount'] = $items;

        if ($autoscroll) {
            $swiper_data_init['autoplay']['delay'] = $auto_play_dealytime;
            $swiper_data_init['autoplay']['disableOnInteraction'] = false;

            if ($autoplayoff) {
                $swiper_data_init['autoplay']['pauseOnMouseEnter'] = $autoplayoff;
            }
        }

        if ($scrollbar) {
            $swiper_data_init['scrollbar']['el'] = "#swiper-scrollbar-" . $this->random_number;
            $swiper_data_init['scrollbar']['hide'] = false;
        }

        if ($navarrow) {
            $swiper_data_init['navigation']['nextEl'] = "#swiper-button-next-" . $this->random_number;
            $swiper_data_init['navigation']['prevEl'] = "#swiper-button-prev-" . $this->random_number;
        }

        if (in_array($paginationtype, ['default', 'dynamic', 'progress', 'fraction', 'custom'])) {
            $swiper_data_init['pagination_id'] = "#swiper-pagination-" . $this->random_number;
            $swiper_data_init['pagination_type'] = $paginationtype;
        }
    }

    return $swiper_data_init;
}

	public function isEnabled(){
		
		$flag = false;
		$status = $this->_listing_data['status'];
		if($status){
			$flag = true;
			if($flag){
				$stores = $this->_listing_data['stores'];
				$str_arr = explode(",",(string)$stores);
				$current_storeId = $this->_storeManager->getStore()->getId();
				if(in_array( 0, $str_arr )){
					$flag = true;
				}elseif(in_array( $current_storeId, $str_arr )){
					$flag = true;
				}else{
					$flag = false;
				}
				
			}
		}
		return $flag;
		
	}
	
	public function getProductShortDescription($description,$length){
		     $product_short_desc = strip_tags((string)$description);
			   
			if(str_word_count($description, 0) > $length){
				$words = str_word_count($description, 2);
				$pos = array_keys($words);
				$description = substr($description, 0, $pos[$length]) . '...';
			}
			return $description;
	}
	
	public function getProductCollectionByType(){
		$product_type_options = $this->general['product_type_options'];
		// || $this->general['collection_type'] == 'manually'
		if($product_type_options == 'featured'){
			$this->setProductCollection($this->getFeaturedproductsCollection());
			//$this->page_param = 'fp';
		}elseif($product_type_options == 'new'){
			$this->setProductCollection($this->getNewProductsCollection());
			//$this->page_param = 'np';
		}elseif($product_type_options == 'mostview'){
			$this->setProductCollection($this->getMostviewedCollection());
			//$this->page_param = 'mp';
		}elseif($product_type_options == 'bestseller'){
			$this->setProductCollection($this->getBestsellerCollection());
			//$this->page_param = 'bp';
		} 
	}
		
	public function getProductCollectionById($product_ids = array()){
		$storeId=$this->_storeManager->getStore()->getId();
		//get values of current page
				
		$this->page_param = 'list'.$this->_listing_id;
       	$page_param = $this->page_param;
		$page=($this->getRequest()->getParam($page_param))? $this->getRequest()->getParam($page_param) : 1;
        
		/** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
		$total_limit = $this->slider_options['num_of_prod'];
	   /* Code Start Comment By Ajay On 07-11-2022 For Set Random Listing */
	   // $product_ids = array_slice($product_ids,0,$total_limit);
	   //for limit the collection to solve last pagination number of items issue
		/* Code end Comment By Ajay On 07-11-2022 For Set Random Listing */
		$collection = $this->_addProductAttributesAndPrices($collection)
            ->setStore($storeId)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('entity_id', ['in' =>$product_ids])
			->addAttributeToFilter('visibility', 4);
		
		 //Set Sort Order
		 /* Code Start Comment By Ajay On 07-11-2022 For Set Random Listing */
		if($this->display_settings['sort_by']=='random') {
			$collection->getSelect()->order('rand()');			
		}else if($this->display_settings['sort_by']=='position') {
			$collection->getSelect()->order('e.entity_id ' . $this->display_settings['sort_order']);
		} else {
			$collection->addAttributeToSort($this->display_settings['sort_by'], $this->display_settings['sort_order']);
		}
		
		
		if(!$this->slider_options['enable_slider']){
            $collection->setPageSize($this->slider_options['items_per_page'])
            ->setCurPage($page);
		}else{
			$collection->setPageSize($total_limit);
		}
		
		
		/* Code End Comment By Ajay On 07-11-2022 For Set Random Listing */
        //Display out of stock products
        if (!$this->display_settings['display_outofstock']) {
            $this->_stockFilter->addInStockFilterToCollection($collection);
        }
		
		//Display By Category
		if ($this->general['display_by']=='cat') {
			$category_ids = $this->_listing_data['category_ids'];
            if ($category_ids) {
                $categories = ltrim($category_ids,",");
                $categorytable = $this->_coreResource->getTableName('catalog_category_product');
                $collection->getSelect()
                        ->joinLeft(['ccp' => $categorytable], 'e.entity_id = ccp.product_id', 'ccp.category_id')
                        ->group('e.entity_id')
                        ->where("ccp.category_id IN (".$categories.")");
            }
        }
		
        /* if($this->display_settings['sort_by']=='position') {
			$collection->getSelect()->order('e.entity_id ' . $this->display_settings['sort_order']);
		} else {
			$collection->addAttributeToSort($this->display_settings['sort_by'], $this->display_settings['sort_order']);
		}
		*/
        return $collection;
	}
	
	public function getBestsellerCollection(){
		$collection_type = $this->general['collection_type'];
		switch ($collection_type) {
			case 'auto':
				$product_ids = $this->getBestsellerAutoProducts();
				break;
			case 'manually': //Manually
				$product_ids = $this->getProductsIds();
				break;
			case 'both': //Both
				$collection1 = $this->getBestsellerAutoProducts();
				$collection2 = $this->getProductsIds();
				$product_ids = array_unique(array_merge($collection1, $collection2));
				break;
			default:
				$product_ids = $this->getProductsIds();
				break;
        }
		$this->_product_ids = $product_ids;
		$collection = $this->getProductCollectionById($product_ids);
		return $collection;
	}
	
	public function getBestsellerAutoProducts()
    {
		$storeId=$this->_storeManager->getStore()->getId();
	 
		$time = $this->general['best_time'];
		$showparent = $this->general['bundle_config'];

		$today = strtotime($this->_localeDate->date()->format('Y-m-d H:i:s'));
		$last = $today - (60*60*24*$time);
		$from = date("Y-m-d H:i:s", $last);
		$to = date("Y-m-d H:i:s", $today);

		$product_ids = [];
		$bestseller_products=$this->_bsautoCollection
						->addOrderedQty($from, $to)
					  //  ->addAttributeToSelect('*')
						->setOrder('ordered_qty', 'desc');

		$bestseller_products->getSelect()->where('order_items.store_id ='.$storeId);

		/** get collection order status wise **/

		$status=$this->general['order_status'];
        if ($status!='all') {
            $s=explode(",", (string)$status);
            $order_status =  "'" . implode("','", $s) . "'";
        }

        if ($status!='all') {
           $bestseller_products->getSelect()->where('order.status IN ('.$order_status.')');
        }

		$bs_data=$bestseller_products->getData();

        foreach ($bs_data as $custom) {
           $product_ids[]=$custom['entity_id'];
	/**Start for display parent and child product for auto configurable and bundle product ***/
		
			if ($showparent=='parent') {
				foreach($product_ids as $key=>$e)
				{
					$config_parent_product =$this->config_product->getParentIdsByChild($e);
					$bundle_parent_product =$this->bundle_product->getParentIdsByChild($e);			
					 if((isset($config_parent_product[0]))||(isset($bundle_parent_product[0]))){
						unset($product_ids[$key]);
					}
				}
			}
	
		/**End for display parent and child product for auto configurable and bundle product ***/
        }
		$this->_product_ids = $product_ids;
		return $product_ids;
    }
    	
	public function getMostviewedCollection(){
		$collection_type = $this->general['collection_type'];
		switch ($collection_type) {
			case 'auto':
				$product_ids = $this->getAutoMostviewedCollection();
				break;
			case 'manually': //Manually
				$product_ids = $this->getProductsIds();
				break;
			case 'both': //Both
				$collection1 = $this->getAutoMostviewedCollection();
				$collection2 = $this->getProductsIds();
				$product_ids = array_unique(array_merge($collection1, $collection2));
				break;
			default:
				$product_ids = $this->getProductsIds();
				break;
        }
		$this->_product_ids = $product_ids;
		$collection = $this->getProductCollectionById($product_ids);
		return $collection;
	}
	
	public function getAutoMostviewedCollection()
    {
		$product_ids=[];
        $collection = $this->reportModel->create();
        $connection=$this->_coreResource->getConnection();
        $viewindextable = $this->_coreResource->getTableName('report_viewed_product_index');
        $select=$collection->getSelect()->reset()->from(
            ['viewed_magebees' => $viewindextable]
        );
        $result=$connection->query($select)->fetchAll();
        foreach ($result as $res) {
            $product_ids[]=$res['product_id'];
        }
        $this->_product_ids = $product_ids;
        return $product_ids;
    }
	
	public function getNewProductsCollection(){
		$collection_type = $this->general['collection_type'];
		switch ($collection_type) {
            case 'auto':
                if ($this->general['date_enabled']) {
                    $collection = $this->getNewProductsByFromToDates();
                } else {
                    $collection = $this->getNewProductsByCreatedDate();
                }
				$product_ids = $collection->getAllIds();
                break;
            case 'manually': //Manually
                $product_ids = $this->getProductsIds();
                break;
            case 'both': //Both
                if ($this->general['date_enabled']) {
                    $collection = $this->getNewProductsByFromToDates();
                } else {
                    $collection = $this->getNewProductsByCreatedDate();
                }
                $selected_product_ids = $this->getProductsIds();
                $product_ids = array_unique(array_merge($collection->getAllIds(), $selected_product_ids));
                 
                break;
            default:
                $selected_product_ids = $this->getProductsIds();
                break;
        }
        	
		$collection = $this->getProductCollectionById($product_ids);
		$this->_product_ids = $product_ids;
		return $collection;
	}
	
	public function getNewProductsByCreatedDate()
    {
        $days = $this->general['new_threshold'];
        $today = strtotime($this->_localeDate->date()->format('Y-m-d H:i:s'));
        $last = $today - (60*60*24*$days);
        $from = date("Y-m-d H:i:s", $last);
        $to = date("Y-m-d H:i:s", $today);
         $collection = $this->_productCollectionFactory->create();
        //$collection->setVisibility($this->visibilityModel->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->addAttributeToSelect('*')
                ->addAttributeToSort('created_at', 'desc')
                ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to]);
                
        return $collection;
    }
	
	public function getNewProductsByFromToDates()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);

        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        );
		
		return $collection;
    }
	
	public function getFeaturedproductsCollection(){
		$product_ids=$this->getProductsIds();
		$this->page_param = 'fp';
		$collection = $this->getProductCollectionById($product_ids);
		//$collection = $this->getProductCollectionTest($product_ids);
		$this->_product_ids = $product_ids;
		return $collection;
	}
	public function getProductsIds()
    {
        $customcollection = $this->_manualCollection->addFieldToFilter('listing_id',$this->_listing_id);
		
		$this->productIds = array_map([$this,"getProductIdsArr"], $customcollection->getData());
		return $this->productIds;
	}
	
	public function getProductIdsArr($element)
    {
        return $element['product_id'];
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
	
	public function getPagerHtml() {
		$count = $this->getProductCollection()->getSize();
        if ($this->getProductCollection()->getSize()) {
			$loaded = $this->getProductCollection()->load();
			$this->page_param = 'list'.$this->_listing_id;
			$random_pager_id = 'listing.pager'.uniqid();
            if (!$this->pager) {
				// check for load list with Ajax
				
					$this->pager = $this->getLayout()->createBlock(
						 'Magento\Catalog\Block\Product\Widget\Html\Pager',
						 $random_pager_id
					 );
				
				 
				 

				$this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    //->setPageVarName('page')
                    ->setPageVarName($this->page_param)
                    ->setLimit($this->slider_options['items_per_page'])
                    ->setTotalLimit($this->slider_options['num_of_prod'])
                    ->setCollection($this->getProductCollection());
            }
			//$this->getProductCollection()->load();
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
	
	public function getAjaxLoadPagerHtml() {
		$count = $this->getProductCollection()->getSize();
        if ($this->getProductCollection()->getSize()) {
			$loaded = $this->getProductCollection()->load();
			$this->page_param = 'list'.$this->_listing_id;
			$random_pager_id = 'listing.pager'.uniqid();
            if (!$this->pager) {
				// check for load list with Ajax
				
					$this->pager = $this->getLayout()->createBlock(
                     'Magebees\Productlisting\Block\AjaxPagerUrl',
                     $random_pager_id
					);
					
				
				 
				 

				$this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    //->setPageVarName('page')
                    ->setPageVarName($this->page_param)
                    ->setLimit($this->slider_options['items_per_page'])
                    ->setTotalLimit($this->slider_options['num_of_prod'])
                    ->setCollection($this->getProductCollection());
            }
			//$this->getProductCollection()->load();
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
	
	public function getViewMoreProductCollection(){
		
		$collection = $this->_productCollectionFactory->create();
		$storeId=$this->_storeManager->getStore()->getId();
		$page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
		$collection = $this->_addProductAttributesAndPrices($collection)
            ->setStore($storeId)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('entity_id', ['in' =>$this->_product_ids])
			->addAttributeToFilter('visibility', 4);
		
		 /* Code Start Comment By Ajay On 07-11-2022 For Set Random Listing */
		if($this->display_settings['sort_by']=='random') {
			$collection->getSelect()->order('rand()');			
		}else if($this->display_settings['sort_by']=='position') {
			$collection->getSelect()->order('e.entity_id ' . $this->display_settings['sort_order']);
		} else {
			$collection->addAttributeToSort($this->display_settings['sort_by'], $this->display_settings['sort_order']);
		}
		
		$itemPerPage = $this->getConfigValue('catalog/frontend/grid_per_page');
		$collection->setPageSize($itemPerPage)
            ->setCurPage($page);
			
		
		
		/* Code End Comment By Ajay On 07-11-2022 For Set Random Listing */
        //Display out of stock products
        if (!$this->display_settings['display_outofstock']) {
            $this->_stockFilter->addInStockFilterToCollection($collection);
        }
		
		
		
		 return $collection;
       
	}
	
	public function getViewMorePagerHtml() {
		$currentMode = $this->getMode();
		if($currentMode=='grid'):
		$itemPerPage = $this->getConfigValue('catalog/frontend/grid_per_page');
		else:
		$itemPerPage = $this->getConfigValue('catalog/frontend/list_per_page');
		endif;
		
		
		$count = $this->getViewMoreProductCollection()->getSize();
        if ($this->getViewMoreProductCollection()->getSize()) {
			
            if (!$this->pager) {
                 $this->pager = $this->getLayout()->createBlock(
                     'Magento\Catalog\Block\Product\Widget\Html\Pager',
                     'list.pager'
                 );
				
				$this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName('p')
                    ->setLimit($itemPerPage)
                    ->setTotalLimit($count)
                    ->setCollection($this->getViewMoreProductCollection());
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
                'Magebees\Productlisting\Block\ResultToolbar',
                'product_list_toolbar'
                )
                ->setTemplate('Magebees_Productlisting::toolbar.phtml')
                ->setCollection($this->getViewMoreProductCollection());
		$this->setChild('toolbar', $toolbar);
		
		
		return $toolbar->toHtml();		
    }
	
	 public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }
}