<?php
namespace Magebees\PocoThemes\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Cms\Model\PageFactory;
use Magento\Catalog\Model\Product;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_format;
	protected $_storeManager;
	protected $_filterProvider;
    protected $_blockFactory;
	protected $productCollectionFactory;
    protected $productVisibility;
    protected $productStatus;
	protected $storeRepository;
	protected $customerGroupColl;	
	protected $_registry;
	protected $_template;
	protected $pageFactory;
	protected $_json;
	protected $productMetadataInterface;
	protected $_objectManager;
	protected $_request;
	protected $moduleReader;
	protected $customerSession;
	protected $_pageFactory;
	protected $_localeCurrency;
	protected $convertArray;
	protected $file;
	protected $cart;
	protected $priceHelper;
	protected $category;
	protected $assetRepository;
	protected $menucreatorgroup;
	protected $pageConfig;
	protected $assetMergeService;
	protected $pageAssetCollection;
	protected $CriticalCss;
	protected $url;
	protected $listProductBlock;
	protected $_imagehelper;
	protected $httpContext;
	protected $currencyFactory;
	protected $_eavConfig;
	protected $httpActionContext;
	protected $jsonEncoder;
	
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Cms\Model\PageFactory $page,
		\Magento\Framework\Locale\CurrencyInterface $localeCurrency,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Store\Api\StoreRepositoryInterface $storeRepository,
		\Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupColl,
		\Magento\Framework\Convert\ConvertArray $convertArray,
		\Magento\Framework\Filesystem\Io\File $file,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Framework\Pricing\Helper\Data $priceHelper,
		\Magento\Catalog\Model\Category $category,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Element\Template $template,
		PageFactory $pageFactory,
		\Magento\Cms\Model\BlockFactory $block,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\View\Asset\Repository $AssetRepository,		\Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatorgroup,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\View\Asset\Collection $pageAssetCollection,
		\Magebees\PocoThemes\Model\CriticalCss $CriticalCss,
		\Magento\Framework\View\Asset\MergeService $assetMergeService,
		\Magento\Framework\Url $url,
		\Magento\Catalog\Block\Product\ListProduct $listProductBlock,
		\Magento\Catalog\Helper\Image $_imagehelper,
		\Magento\Framework\Locale\Format $_format,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Directory\Model\CurrencyFactory $currencyFactory,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Framework\App\Action\Context $http_action_context,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder
	) { 
		$this->productMetadataInterface = $productMetadataInterface;
		$this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
		$this->_request = $request;
		$this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        $this->moduleReader = $moduleReader;
		$this->customerSession = $customerSession;
		$this->_pageFactory = $page;
		$this->_localeCurrency = $localeCurrency;
		$this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
		$this->storeRepository = $storeRepository;
		$this->customerGroupColl = $customerGroupColl;	
		$this->convertArray = $convertArray;
		$this->file         = $file;
		$this->cart         = $cart;
		$this->priceHelper  = $priceHelper;
		$this->category  = $category;
		$this->_registry = $registry;
		$this->_template = $template;
		$this->pageFactory = $pageFactory;
		$this->_json = $json;
		$this->assetRepository = $AssetRepository;
		$this->menucreatorgroup = $menucreatorgroup;
		$this->pageConfig = $pageConfig;
		$this->assetMergeService = $assetMergeService;
		$this->pageAssetCollection = $pageAssetCollection;
		$this->CriticalCss = $CriticalCss;
		$this->url = $url;
		$this->listProductBlock = $listProductBlock;
		$this->_imagehelper = $_imagehelper;
		$this->_format = $_format;
		$this->httpContext = $httpContext;
		$this->currencyFactory = $currencyFactory;
		$this->_eavConfig = $eavConfig;
		$this->httpActionContext = $http_action_context;
		$this->jsonEncoder = $jsonEncoder;
		parent::__construct($context);
	}
	public function isProductAttributeExists($field)
    {
		$attr = $this->_eavConfig->getAttribute(Product::ENTITY, $field);
		return ($attr && $attr->getId());
    }
	public function getUrlByKey($key){
		$url = rtrim($this->url->getUrl($key), '/');
		return $url;
	}
	public function getAllCriticalCss($critical_css_types){
		$criticalCssList = array();
		
		if((isset($critical_css_types['type']))&&(isset($critical_css_types['storeId'])))
		{
			$storeId = $critical_css_types['storeId'];
			$cssTypes = $critical_css_types['type'];
			
			$criticalTypeCss = $this->CriticalCss->getCollection()
				 ->addFieldToFilter('stores', $storeId)
				 ->addFieldToFilter('type',array('in'=> $cssTypes ));
				
			foreach($criticalTypeCss as $typecss){
				$criticalCssList[$typecss['type']] = $typecss['css'];
			}
		}
		if((isset($critical_css_types['menu_group']))&&(isset($critical_css_types['menu_group'])))
		{
			$menu_groups = $critical_css_types['menu_group'];
			$MenuGroupCriticalCss = $this->CriticalCss->getCollection()
			 ->addFieldToFilter('type', 'menu_group')
			 ->addFieldToFilter('stores',array('in'=> $menu_groups ));
			foreach($MenuGroupCriticalCss as $menuGroup){
				$criticalCssList[$menuGroup['stores']] = $menuGroup['css'];
			}
		}
		
		
		
		
		/* Swiper Slider Lib Start */
		if(isset($critical_css_types['swiper_slider'])){
			$css_type = 'swiper_slider';
			$criticalCssList[$css_type] = $this->getCriticalCss('0',$css_type);
		}
		
		if(isset($critical_css_types['slider_group'])){
			$css_type = 'slider_group';
			$slider_group = $critical_css_types['slider_group'];
			$criticalCssList[$css_type] = $this->getCriticalCss($slider_group,$css_type);
		}
		return $criticalCssList;
		
		
	}
	
	public function getCriticalCss($storeId,$css_type){
		
		$criticalCss = $this->CriticalCss->getCollection()
			 ->addFieldToFilter('stores', $storeId)
			 ->addFieldToFilter('type', $css_type);
		 if($criticalCss->getSize()>0){
			 return $criticalCss->getFirstItem()->getCss();
		 }
		 return null;
	}
	public function getMergeCss(){
		$merge_css = '';
		
		foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {
        


            if ($group->getProperty('can_merge') && count($group->getAll()) > 1) {
            $groupAssets = $this->assetMergeService->getMergedAssets(
                $group->getAll(),
                'css'
            );
			
			 foreach ($groupAssets as $asset) {
				// print_r(get_class_methods($asset));
				if($asset->getSourceContentType()=='css'){
					$merge_css .= $asset->getContent();			
				}
			
			 }
			}
		return $merge_css;
        }
	}
	public function getMergeJs(){
		$merge_js = '';
		
		foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {
        


            if ($group->getProperty('can_merge') && count($group->getAll()) > 1) {
            $groupAssets = $this->assetMergeService->getMergedAssets(
                $group->getAll(),
                'js'
            );
			
			 foreach ($groupAssets as $asset) {
				print_r($asset->getPath());
				}
			}
		return $merge_js;
        }
	}
	public function getViewDirectory()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Magebees_PocoThemes'
        );
        return $viewDir;
    }

	public function isStoreMultiStoreView($store_id){
			
		$groupId = $this->_storeManager->getStore($store_id)->getGroupId();
		$groups = $this->_storeManager->getWebsite()->getGroups();
		$storeViewName = [];
		foreach ($groups as $key => $group) {
			if($groupId == $group->getId()){
				$stores = $group->getStores();
				foreach ($stores as $store) {
					if(isset($storeViewName[$groupId]))
					{
						return true;
					}else{
					$storeViewName[$groupId] = $store->getName(); // get store view name	
					}
				}
			}
		}
    
	}
	public function isMultipleCurrency($store_id)
	{
		$allowed_currency = $this->scopeConfig->getValue('currency/options/allow',ScopeInterface::SCOPE_STORE,$store_id);
		if(count(explode(",",$allowed_currency))>1):
		return true;
		endif;
		return false;
	}
	
	public function getStoreId(){
		return $this->_storeManager->getStore()->getId();
	}
	public function getWebsiteId($storeId){
		    return $this->_storeManager->getStore($storeId)->getWebsiteId();
	}
	public function getCurrentAction(){
		return $this->_request->getFullActionName();
	}
	
	public function getStoreCode(){
		return $this->_storeManager->getStore()->getCode();
	}
	
	public function getUrl(){
		return $this->_storeManager->getStore()->getBaseUrl();
	}
	public function getRootCategoryId(){
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
		$storeId = $this->_storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
		return $rootCategoryId = $this->_storeManager->getStore($storeId)->getRootCategoryId();

	}
	public function getWebUrl()
    {
		$storeId = $this->_storeManager->getStore()->getId();
		$use_store = $this->scopeConfig->getValue('web/url/use_store', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);	
		$baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		if($use_store):
			$storeCode = $this->_storeManager->getStore($storeId)->getCode();
			return $baseUrl.$storeCode;
		else:
			return $baseUrl;
		endif;
	}
	public function getUrlWithoutStoreCode($store_id){
		$section = 'web';
		$group = 'secure';
		$field = 'use_in_frontend';
		$use_in_frontend = $this->scopeConfig->getValue($section.'/'.$group.'/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		if($use_in_frontend){
			$section = 'web';
			$group = 'secure';
			$field = 'base_url';
			$baseUrl = $this->scopeConfig->getValue($section.'/'.$group.'/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		}else{
			$section = 'web';
			$group = 'unsecure';
			$field = 'base_url';
			$baseUrl = $this->scopeConfig->getValue($section.'/'.$group.'/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		}
		
		return $baseUrl;
		
	}
	public function getCurrentPriceFormat($store_Id){
		$currencyCode = $this->_storeManager->getStore($store_Id)->getCurrentCurrencyCode();
		$current_price_format = $this->_format->getPriceFormat(null,$currencyCode);
		return $this->getJsonEncode($current_price_format);
	}
	public function getFreeShippingCurrentPrice($free_shipping_subtotal,$store_Id){
		
		$currentCurrencyCode = $this->_storeManager->getStore($store_Id)->getCurrentCurrency()->getCode();
		$baseCurrencyCode = $this->_storeManager->getStore($store_Id)->getBaseCurrency()->getCode();
		if($baseCurrencyCode!=$currentCurrencyCode){
			
			$rate = $this->currencyFactory->create()
                ->load($baseCurrencyCode)
                ->getAnyRate($currentCurrencyCode);
				$free_shipping_subtotal = $free_shipping_subtotal * $rate;
				
				$free_shipping_subtotal = number_format($free_shipping_subtotal,2);
				
				
			return 	$free_shipping_subtotal;
		}else{
		return 	$free_shipping_subtotal;
		}
		
	}
	public function getMediaUrl(){
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}

	public function getConfigValue($group,$field,$section='pocothemes',$store_id=0){
		return $this->scopeConfig->getValue($section.'/'.$group.'/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
	}	
	 
	public function getConfigGroup($group,$store_id=0){
		return $this->scopeConfig->getValue('pocothemes/'.$group,ScopeInterface::SCOPE_STORE,$store_id);
	}
    
    public function getGeneral($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/general/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
	
	public function getThemeLayout($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/theme_layout/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}

	public function getFontIcon($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/font_icons/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
    
    public function getHeader($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/header/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
    }
	
    public function getHome($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/home/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
    }
	
	public function getSidebar($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/sidebar/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
    }
	
    
	public function getProductListing($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/pro_list/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}	
	
	public function getProductDetail($field,$store_id=0){
		
		return $this->scopeConfig->getValue('pocothemes/pro_view/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
	public function isRecentProductsSidebar($field='recent_prod_pos'){
		$field = 'recent_prod_pos';
		$store_id = $this->_storeManager->getStore()->getId();
		$value = $this->scopeConfig->getValue('pocothemes/pro_view/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		if($value=='sidebar'){
			return true;
		}
		return false;
	}
	public function isRecentProductsBottom($field='recent_prod_pos'){
		$field = 'recent_prod_pos';
		$store_id = $this->_storeManager->getStore()->getId();
		$value = $this->scopeConfig->getValue('pocothemes/pro_view/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		if($value=='bottom'){
			return true;
		}
		return false;
	}
    public function getProductDetailXml($field){
		$store_id = $this->_storeManager->getStore()->getId();
		return $this->scopeConfig->getValue('pocothemes/pro_view/'.$field,ScopeInterface::SCOPE_STORE,$store_id);
		;		
	}
    public function getFooter($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/footer/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
    
	public function getSocial($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/socials/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
    
    public function getContactus($field,$store_id=0){
        return $this->scopeConfig->getValue('pocothemes/contactus/'.$field,ScopeInterface::SCOPE_STORE,$store_id);		
	}
	    
	public function getAdvanced($name,$store_id=0){
		return $this->scopeConfig->getValue('pocothemes/advanced/'.$name,ScopeInterface::SCOPE_STORE,$store_id);		
	}
	public function getDeveloper($name,$store_id=0){
		return $this->scopeConfig->getValue('pocothemes/developer/'.$name,ScopeInterface::SCOPE_STORE,$store_id);		
	}
	public function getCurrentMagentoVersion(){
		return $this->productMetadataInterface->getVersion();
	}
	
	public function getCategoryProductIds($current_category) {
        $category_products = $current_category->getProductCollection()
            ->addAttributeToSelect('*')
            //->addAttributeToFilter('is_saleable', [1], 'left')
            ->addAttributeToSort('position','asc');
        $cat_prod_ids = $category_products->getAllIds();
        
        return $cat_prod_ids;
    }
	
	public function getPrevProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos - 1])) {
            $prev_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos - 1]);
            return $prev_product;
        }
        return false;
    }

    
	
    public function getNextProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos + 1])) {
            $next_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos + 1]);
            return $next_product;
        }
        return false;
    }
		
	//for custom tabs
	public function subval_sort($a,$subkey) {
        foreach($a as $k=>$v) {
            $b[$k] = strtolower($v[$subkey]);
        }
        asort($b);
        foreach($b as $key=>$val) {
            $c[] = $a[$key];
        }
        return $c;
    }
		
	public function getStaticBlockDetails($block_id){
		$block_data = null;
		$store_id = $this->getStoreId();
		$block = $this->_blockFactory->create();
		$block_data = $block->setStoreId($store_id)->load($block_id,'identifier');
		
		if(($block_data)&&($block_data->getIsActive())){
						$block_content = $block_data->getContent();
						if($block_content){
							$content = $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block_content);
							return $content;
						}
		}
		return null;
	}
	
	public function getStoreFeaturesBlocks(){
		$store_id = $this->getStoreId();
		$store_features = $this->getHome('store_features',$store_id);
		
		if($store_features){
			$store_features = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($store_features);
		}
		
		return $store_features;
	}

	public function getSocialMedia(){
		$store_id = $this->getStoreId();
		$social_media = $this->getSocial('social_media',$store_id);
		
		if($social_media){
			$social_media = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($social_media);
		}
		
		return $social_media;
	}
	public function getJsonSalesNotificationLocation($sales_notificationConfig,$storeId){
		$list_locations = null;
		$sales_notification_locations = array();
		if(isset($sales_notificationConfig['list_locations'])){
				$list_locations = $sales_notificationConfig['list_locations'];			
		}
		if($list_locations){
			$list_locations = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($list_locations);
		}
		$count=0;
		foreach($list_locations as $location):
		$count++;
			$sales_notification_locations[$count] = array('name'=>trim(preg_replace('/\s\s+/', ' ', $location['location'])));
		endforeach;
		return $this->jsonEncoder->encode($sales_notification_locations);
		
	}
	public function getBrowserTabNotificationMessage($browser_tab_notification_message){
		
		$messages = array();
		$notification_message = array();
		if($browser_tab_notification_message){
			$notification_message = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($browser_tab_notification_message);
		}
		$count=0;
		foreach($notification_message as $message):
			$count++;
			$messages[$count] = trim(preg_replace('/\s\s+/', ' ', $message['message']));
		endforeach;
		return $this->jsonEncoder->encode($messages);
	
	}
	 public function getCustomTabs($product){
		$store_id = $this->getStoreId();
        $custom_tabs = $this->getProductDetail('custom_tabs',$store_id);
        if($custom_tabs){
			$custom_tabs = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($custom_tabs);
		}
		    
        $custom_tabs_array = array();
        if(count($custom_tabs) > 0){
            foreach($custom_tabs as $tab) {
				$arr = array();
				$arr['tab_title'] = $tab['tab_title'];
				$content = $tab['tab_content'];
            	$block_id = $tab['staticblock_id'];
				if($block_id){
					$block = $this->_blockFactory->create();
					$block->setStoreId($store_id)->load($block_id);
					if($block){
						$block_content = $block->getContent();
						if($block_content){
							$content = $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block_content);
						}
					}
				}
				
				$attr_code = $tab['attribute_code'];
				if($attr_code){
					$attribute = $product->getResource()->getAttribute($attr_code);
					if($attribute){
						$attr_value = $attribute->getFrontend()->getValue($product);
						if($attr_value){ 
							$content = $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($attr_value);
						}
					}
				}
							
				$arr['tab_content'] = $content;
				$arr['sort_order'] = (!$tab['sort_order'] || !is_numeric($tab['sort_order']))?0:$tab['sort_order'];
				$custom_tabs_array[] = $arr;
                
            }
        }
        if(count($custom_tabs_array) > 0)
            $custom_tabs_array = $this->subval_sort($custom_tabs_array,'sort_order');
        
        return $custom_tabs_array;
    }
	public function getBlockContentByIdentifier($blockIdentifier)
	{
		$store_id = $this->getStoreId();
		if($blockIdentifier){
			$block = $this->_blockFactory->create();
			$block->setStoreId($store_id)->load($blockIdentifier);
			if($block){
				$block_content = $block->getContent();
				if($block_content){
					return $content = $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block_content);
					}
				}
			}
			return null;
	}
	
	public function getSliderIdFromUniqueCode($slider_group_code){
    	
    	$group_code = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Responsivebannerslider')->load($slider_group_code,'unique_code')->getId();

    	return $group_code;
    }
	
	public function getStripDescription($short_desc,$length)
	{
		$short_desc = strip_tags($short_desc);
		if (strlen($short_desc) > $length) {
		$product_short_desc_cut = substr($short_desc, 0, $length);
		$product_short_desc_cut_end = strrpos($product_short_desc_cut, ' ');		
		$product_short_desc = $product_short_desc_cut_end? substr($product_short_desc_cut, 0, $product_short_desc_cut_end) : substr($product_short_desc_cut, 0);
		$product_short_desc .= '...'; 
		return $product_short_desc;
		}
		return $short_desc;
	}
	public function isLoggedIn(){
		
		$isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
		
		if($this->customerSession->isLoggedIn()) :
			return true;
		else:
			return false;
		endif;
	}
	
	public function getActionName(){
		return $this->httpActionContext->getRequest()->getFullActionName();
	}
	public function isCmsPageExists($identifier){
		
		$current_storeid = $this->getStoreId();
		$cms_page =  $this->_pageFactory->create()->setStoreId($current_storeid)->load($identifier);
		$page_active_check = $cms_page->getIsActive();
		if ($page_active_check == "1") {
			return true;
		}	
		return false;
	}
	public function getCurrentCurrencySymbol($code)
	{
	return $this->_localeCurrency->getCurrency($code)->getSymbol();
	}
public function getStoreProductIds($count){
		$productCollections = $this->productCollectionFactory->create();
		$productCollections->getSelect()->orderRand();
        $productCollections->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
		$productCollections->addAttributeToFilter('type_id', ['nin' => 'bundle']);
        $productCollections->setVisibility($this->productVisibility->getVisibleInSiteIds());
		$productCollections->setPageSize($count); // fetching only 3 products
        
		
		$result = array();
		$productIds = array();
		$productSkus = array();
		$result['product_ids'] = null;
		$result['product_sku'] = null;
		foreach($productCollections as $product):
			$productIds[] =  $product->getId();
			$productSkus[] =  $product->getSku();
		endforeach;
		if(count($productIds)>0)
		{
			$result['product_ids'] = implode(",",$productIds);
		}
		if(count($productSkus)>0)
		{
			$result['product_sku'] = implode(",",$productSkus);
		}
		return $result;
		
		
	}
	public function getCurrentStoreProductIds($count,$join){
		$productCollections = $this->productCollectionFactory->create();
		$productCollections->getSelect()->orderRand();
        $productCollections->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
		$productCollections->addAttributeToFilter('type_id', ['nin' => 'bundle']);
        $productCollections->setVisibility($this->productVisibility->getVisibleInSiteIds());
		$productCollections->setPageSize($count); // fetching only 3 products
        
		
		$result = array();
		$productIds = array();
		$productSkus = array();
		$result['product_ids'] = null;
		$result['product_sku'] = null;
		foreach($productCollections as $product):
			$productIds[] =  $product->getId();
			$productSkus[] =  $product->getSku();
		endforeach;
		if(count($productIds)>0)
		{
			$result['product_ids'] = implode($join,$productIds);
		}
		if(count($productSkus)>0)
		{
			$result['product_sku'] = implode($join,$productSkus);
		}
		return $result;
		
		
	}
	public function getAllStoreIds(){
		
		$result = array();
		$stores = $this->storeRepository->getList();
		$storeIds = array();
		$result['store_ids_all'] = 0;
		$result['store_ids_str'] = null;
		$result['store_ids_arr'] = null;
		foreach($stores as $store):
			$storeIds[] =  $store->getId();
		endforeach;
		if(count($storeIds)>0)
		{
			$result['store_ids_str'] = implode(",",$storeIds);
			$result['store_ids_arr'] = $storeIds;
		}
		return $result;
	}
	public function getAllBlogStoreIds(){
		$result = array();
		$stores = $this->storeRepository->getList();
		$storeIds = array();
		$storeIds[] = 0;
		foreach($stores as $store):
			$storeIds[] =  $store->getId();
		endforeach;
		if(count($storeIds)>0)
		{
			return $store_list = implode(",",$storeIds);
		}
	}
	public function getCustomerGroupIds() {
		$customerGroups = $this->customerGroupColl->toOptionArray();
		$customerGroupsIds = array_column($customerGroups, 'value');
		$customerGroupsIds_arr = null;
		if(count($customerGroupsIds)>0)
		{
			$customerGroupsIds_arr = implode(",",$customerGroupsIds);
		}
		return $customerGroupsIds_arr;
	}	
	public function getCartGrandTotal(){
		$grandTotal = $this->cart->getQuote()->getGrandTotal();
		return $formattedgrandTotal = $this->priceHelper->currency($grandTotal, true, false);
	}
	public function getFormattedPrice($price){
		return $formattedprice = $this->priceHelper->currency($price, true, false);
	}
	public function getCategory($catId,$storeId){
		return $category = $this->category->setStoreId($storeId)->load($catId);
	}
	public function getLoadingIcon($storeId=null)
	{
		if(!$storeId){
				$storeId	=  $this->getStoreId();
		}
		$current_theme_loading = 'current_theme_loading_'.$storeId;
		
		if($this->_registry->registry($current_theme_loading)):
		
			return $this->_registry->registry($current_theme_loading);
		else:
		
			$loadingIcon = $this->getThemeLayout('theme_loading_image',$storeId);
			if($loadingIcon):
				$mediaURL = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
					$theme_loadingIcon = $mediaURL.'poco/loadingicon/'.$loadingIcon;
					if($this->_registry->registry($current_theme_loading)):
						$this->_registry->unregister($current_theme_loading);
					endif;
					$this->_registry->register($current_theme_loading, $theme_loadingIcon);
					return $mediaURL.'poco/loadingicon/'.$loadingIcon;
				else:
					$theme_loadingIcon = $this->_template->getViewFileUrl('images/poco-loading.png'); 
					if($this->_registry->registry($current_theme_loading)):
						$this->_registry->unregister($current_theme_loading);
					endif;
					$this->_registry->register($current_theme_loading, $theme_loadingIcon);
					return $theme_loadingIcon;
			endif;
		endif;
		
		return null;
	}
	public function getCMSPageId($identifier) {
	$page = $this->pageFactory->create()->load($identifier);	
	if($page->getId()):
	return $page->getId();
	endif;
	return false;
	}
	public function getStaticBlockId($identifier) {
	$block = $this->_blockFactory->create()->load($identifier);	
	if($block->getId()):
	return $block->getId();
	endif;
	return false;
	}
	public function getJsonEncode($data)
   {
	  return json_encode($data,JSON_FORCE_OBJECT|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE|JSON_NUMERIC_CHECK|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_LINE_TERMINATORS|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
	  // return json_encode($data,JSON_FORCE_OBJECT|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE|JSON_NUMERIC_CHECK|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRESERVE_ZERO_FRACTION|JSON_PRETTY_PRINT|JSON_UNESCAPED_LINE_TERMINATORS|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
	   //return json_encode($data,JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS);
   }
   public function getJsonDecode($data)
   {
	   return json_decode($data,true);
       
   }
   public function getStoreMediaUrl($storeId){
	
	return $mediaUrl = $this ->_storeManager-> getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	
   }
   public function getPubAbsolutePath(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
		return $pubPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)->getAbsolutePath();
   }
	public function getAssetUrl($asset){
		return $this->assetRepository->createAsset($asset)->getUrl();
	}
	public function getIdFromUniqueCode($unique_code){  
	$group_id = $this->menucreatorgroup->load($unique_code,'unique_code')->getId();        return $group_id;    
	}	
	public function getMenuCssPath($unique_code,$store_Id){
		
		
		
		
		$groupId = $this->menucreatorgroup->load($unique_code,'unique_code')->getId();       
		$group_details = $this->menucreatorgroup->load($groupId);   
		$menu_type = trim($group_details->getMenutype());
		$_root_point_pub_config = $this->getConfigValue('general','root_point_pub','navigationmenu',$store_Id);					
		$cssurl = $this->_storeManager->getStore($store_Id)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."magebees/navigationmenu/css/".$menu_type."-".$groupId.".css";		 			
		if($_root_point_pub_config)		
		{				 
		$cssurl = str_replace("pub/","",$cssurl);
		}				
		return $cssurl;	
		
		
	}
	
	public function exportConfigXML($configValues){
		$xmlContents = $this->convertArray->assocToXml($configValues,'default');
        // convert it to xml using asXML() function
		$content = $xmlContents->asXML();
       	$this->file->write("exportConfigXML.xml", $content);
			
	}
	public function getAddToCartPostParams($product)
	{
		return $this->listProductBlock->getAddToCartPostParams($product);
	}
	public function getSalesNotificationProducts($product_skus,$storeId){
		
		$product_skus = explode(",",(string)$product_skus);
		$product_skus = array_map('trim', $product_skus);
		$productCollections = $this->productCollectionFactory->create();
		$productCollections->getSelect()->orderRand();
		$productCollections->addStoreFilter($storeId); 
		$productCollections->addAttributeToSelect('name','image');
		$productCollections->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
		$productCollections->setVisibility($this->productVisibility->getVisibleInSiteIds());
		$productCollections->addAttributeToFilter('sku', ['in' => $product_skus]);
		
		
		$products = array();
		foreach($productCollections as $product):
		$product_id = $product->getId(); 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
			$productImage = $this->_imagehelper->init($product, 'category_page_grid')->constrainOnly(FALSE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(110,110);
			$productImageUrl = $productImage->getUrl();
			$products[] =  array('name'=>$product->getName(),'image'=>$productImageUrl,'url'=>$product->getProductUrl());$product->getId();
		endforeach;
		return $products;
		
		
	}
	public function getJsonSalesNotificationProducts($product_skus,$storeId){
		
		$product_skus = explode(",",$product_skus);
		$product_skus = array_map('trim', $product_skus);
		$productCollections = $this->productCollectionFactory->create();
		$productCollections->getSelect()->orderRand();
		$productCollections->addStoreFilter($storeId); 
		$productCollections->addAttributeToSelect('name','image');
		$productCollections->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
		$productCollections->setVisibility($this->productVisibility->getVisibleInSiteIds());
		$productCollections->addAttributeToFilter('sku', ['in' => $product_skus]);
		
		
		$products = array();
		foreach($productCollections as $product):
		$product_id = $product->getId(); 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
			$productImage = $this->_imagehelper->init($product, 'category_page_grid')->constrainOnly(FALSE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(110,110);
			$productImageUrl = $productImage->getUrl();
			$products[$product_id] =  array('name'=>$product->getName(),'image'=>$productImageUrl,'url'=>$product->getProductUrl());$product->getId();
		endforeach;
		
		return $this->jsonEncoder->encode($products);
		
		
		
	}
	public function getExitIntentPopupProducts($product_skus,$storeId){
		$product_skus = explode(",",(string)$product_skus);
		$product_skus = array_map('trim', $product_skus);
		$productCollections = $this->productCollectionFactory->create();
		$productCollections->getSelect()->orderRand();
		$productCollections->addStoreFilter($storeId); 
		//$productCollections->addAttributeToSelect('name','image');
		$productCollections->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
		$productCollections->setVisibility($this->productVisibility->getVisibleInSiteIds());
		$productCollections->addAttributeToFilter('sku', ['in' => $product_skus]);
		return $productCollections;
		
		
	}
}