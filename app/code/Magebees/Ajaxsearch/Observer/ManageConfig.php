<?php
namespace Magebees\Ajaxsearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ManageConfig implements ObserverInterface
{

	protected $_stores = array();
	  protected $_scopeConfig;
      protected $searchbox;
      protected $httpRequest;
      protected $resourceConnection;
      protected $dirReader;
      protected $helper;
      protected $_storeManager;
       protected $_store;

    public function __construct(
        \Magebees\Ajaxsearch\Block\Searchbox $searchbox,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magebees\Ajaxsearch\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
		  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader
    ) {
    
        $this->searchbox = $searchbox;
        $this->helper = $helper;
        $this->resourceConnection = $resourceConnection;
        $this->httpRequest = $httpRequest;
        $this->dirReader = $dirReader;
		$this->_storeManager = $storeManager;
		   $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
		if ($observer->getEvent()->getStore()) {
            $scope = 'stores';
            $scopeId = $observer->getEvent()->getStore();
			$store = $this->_storeManager->getStore($scopeId);
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$this->_store[$store->getStoreId()] = $store->getCode() ;
        } elseif ($observer->getEvent()->getWebsite()) {
            $scope = 'websites';
            $scopeId = $observer->getEvent()->getWebsite();
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$stores = $this->_storeManager->getWebsite($scopeId)->getStores();
			foreach($stores as $store) {
				$this->_store[$store->getStoreId()] = $store->getCode() ;
			}
        } else {
            $scope = 'default';
            $scopeId = 0;
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$stores = $this->_storeManager->getStores();
			foreach($stores as $store) {
				$this->_store[$store->getStoreId()] = $store->getCode() ;
			}
        }
  
        $config=$this->_scopeConfig->getValue('ajaxsearch/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$store_id);
		
        if ($config['enable']) {
            $postdata=$this->httpRequest->getPost()->toArray();
            $layout_config=$this->helper->getLayoutSetting();
            if (isset($postdata['groups']['attributes']['fields'])) {
                $attribute_post=$postdata['groups']['attributes']['fields'];
                $connection=$this->resourceConnection->getConnection();
        
                if ($attribute_post) {
                    foreach ($attribute_post as $key => $value) {
                        if (isset($value['value'])) {
                            $weight=$value['value'];
                            $attr_key=explode("_", $key);
                            $updateData = ['search_weight'=>$weight];
                            $whereCondition = ['attribute_id=?' =>$attr_key['1']];
                            $table=$this->resourceConnection->getTableName('catalog_eav_attribute');
                            $connection->update($table, $updateData, $whereCondition);
                        }
                    }
                }
            }
            if ($layout_config) {
				$this->generateConfigCss();
                
            }
        }
    }
	public function generateConfigCss(){
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$moduleviewDir=$this->dirReader->getModuleDir('view', 'Magebees_Ajaxsearch');
                $cssDir=$moduleviewDir.'/frontend/web/css';
		  if (!file_exists($cssDir)) {
                    mkdir($cssDir, 0777, true);
                }
		foreach($this->_store as $store_id=>$store_code){
			$content = '';			
				$cat_box_bg_color=$this->helper->getGeneral('cat_box_bg_color',$store_id);		 	
                $cat_text_color=$this->helper->getGeneral('cat_text_color',$store_id);            
            
                $popup_bg_color=$this->helper->getGeneral('popup_bg_color',$store_id);               
                $popup_border_color=$this->helper->getGeneral('popup_border_color',$store_id);       
                $searchtag_highlight_color=$this->helper->getGeneral('searchtag_highlight_color',$store_id);
               
            
                $section_title_bg_color=$this->helper->getGeneral('section_title_bg_color',$store_id);              
                $section_title_color=$this->helper->getGeneral('section_title_color',$store_id);     
                $section_title_link_color=$this->helper->getGeneral('section_title_link_color',$store_id);
               
            
                $searchtag_font_color=$this->helper->getGeneral('searchtag_font_color',$store_id);   
                //$searchtag_font_bg_color=$this->helper->getGeneral('searchtag_font_bg_color',$store_id);               
                $searchtag_font_hover_color=$this->helper->getGeneral('searchtag_font_hover_color',$store_id);     
            
            
                $productlist_font_color=$this->helper->getGeneral('productlist_font_color',$store_id);                 
                $productlist_bg_hover_color=$this->helper->getGeneral('productlist_bg_hover_color',$store_id);                 
                $productlist_bg_color=$this->helper->getGeneral('productlist_bg_color',$store_id);  
            
			
			
                $content.='.mbAjaxSearch .select-wrapper .holder { color:'.$cat_text_color.'; background-color:'.$cat_box_bg_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch .select-wrapper .holder:after { border-top-color:'.$cat_text_color.'; }'.PHP_EOL;

                /** for search popup box */
                $content.='.mbAjaxSearch #search_autocomplete { background-color:'.$popup_bg_color.'; border-color:'.$popup_border_color.'; }'.PHP_EOL;
            
            
                /** for search section title */
                $content.='.mbAjaxSearch .mbSecTitle { background-color:'.$section_title_bg_color.';color:'.$section_title_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch .mbSecTitle a { color:'.$section_title_link_color.'; }'.PHP_EOL;
            
                /** for search Tags content */
                $content.='.mbAjaxSearch ul.searchTags { background-color:'.$popup_bg_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch .searchTags .searchTag a { color:'.$searchtag_font_color.';}'.PHP_EOL;
                //$content.='.mbAjaxSearch .searchTags .searchTag a { color:'.$searchtag_font_color.'; background-color:'.$searchtag_font_bg_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch .searchTags .searchTag a:hover { background-color:'.$searchtag_font_hover_color.'; }'.PHP_EOL;
            
                /** for product content */
                $content.='.mbAjaxSearch ul#ajax_ul > li { background-color:'.$productlist_bg_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch ul#ajax_ul > li:hover { background-color:'.$productlist_bg_hover_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch ul#ajax_ul > li p { color:'.$productlist_font_color.'; }'.PHP_EOL;
                $content.='.mbAjaxSearch ul> li#products { background-color:'.$section_title_bg_color.'; }'.PHP_EOL;
            
                /** for cms search content */
                $content.='.searchText { background-color:'.$searchtag_highlight_color.'; }'.PHP_EOL;
                
								
				$css_file_name = "dynamic_search".$store_code.".css";
				 $path_css = $cssDir.'/'.$css_file_name;
                file_put_contents($path_css, $content);
							
			
		}
	}
}
