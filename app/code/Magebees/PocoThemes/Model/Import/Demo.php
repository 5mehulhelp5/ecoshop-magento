<?php
namespace Magebees\PocoThemes\Model\Import;

use Magento\Framework\DataObject;

class Demo
{
	protected $_importPath; 
    protected $_parser;
    private $configCollection;
    protected $helper;
	protected $pocoHelper;
	protected $_storeManager;
	protected $_ajaxsearch_helper;
	protected $_menu_helper;
	protected $_menucreatergroup;
	protected $_configFactory;
	protected $moduleReader;
	protected $directory_list;
	protected $assetRepository;
	protected $fileSystem;
	protected $file;
	protected $_store;
	 
	
    public function __construct(
        \Magebees\PocoBase\Helper\Data $pocoHelper,
		\Magebees\PocoThemes\Helper\Data $helper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magebees\Ajaxsearch\Helper\Data $ajaxsearch_helper,
		\Magebees\Navigationmenu\Helper\Data $menu_helper,
		\Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatergroup,
		\Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory,
		\Magento\Framework\Module\Dir\Reader $moduleReader,
		\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
		\Magento\Framework\View\Asset\Repository $assetRepository,
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollection

    ) {
        $this->pocoHelper = $pocoHelper;
		$this->helper = $helper;
		$this->_storeManager = $storeManager;
		$this->_ajaxsearch_helper = $ajaxsearch_helper;
		$this->_menu_helper = $menu_helper;
		$this->_menucreatergroup = $menucreatergroup;
		$this->_configFactory = $configFactory;
		$this->moduleReader = $moduleReader;
		$this->directory_list = $directory_list;
		$this->assetRepository = $assetRepository;
		$this->fileSystem = $fileSystem;
		$this->file = $file;
		$this->configCollection = $configCollection;
        $this->_parser = new \Magento\Framework\Xml\Parser();
    }

     public function getDirectory()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Magebees_PocoThemes'
        );
        return $viewDir;
    }

    public function importDemo($demo_version,$store=NULL,$website = NULL)
    {
        // Default response
        $gatewayResponse = new DataObject([
            'is_valid' => false,
            'import_path' => '',
            'request_success' => false,
            'request_message' => __('Error during Import '.$demo_version.'.'),
        ]);

        try {
            
            $this->_importPath = $this->getDirectory();
            $xmlPath = $this->_importPath .'/importdata/demo/'. $demo_version . '.xml';
            $overwrite = true;
            
            if (!is_readable($xmlPath))
            {
                throw new \Exception(
                    __("Can't get the data file for import ".$demo_version.": ".$xmlPath)
                );
            }
            $data = $this->_parser->load($xmlPath)->xmlToArray();
            $scope = "default";
            $scope_id = 0;
            if ($store && $store > 0) // store level
            {
                $scope = "stores";
                $scope_id = $store;
				$store = $this->_storeManager->getStore($scope_id);
				$this->_store[$store->getStoreId()] = $store->getCode() ;
				
            }
            elseif ($website && $website > 0) // website level
            {
                $scope = "websites";
                $scope_id = $website;
				$stores = $this->_storeManager->getWebsite($scope_id)->getStores();
				foreach($stores as $store) {
					$this->_store[$store->getStoreId()] = $store->getCode() ;
				}
            }else{
				 $scope = 'default';
				$scope_id = 0;
				$stores = $this->_storeManager->getStores();
				foreach($stores as $store) {
					$this->_store[$store->getStoreId()] = $store->getCode() ;
				}
			}
			
           foreach($data['root']['config'] as $b_name => $b){
				foreach($b as $c_name => $c){
					if(is_array($c)){
						foreach($c as $d_name => $d){
						if(is_array($d))
						{
							foreach($d as $e_name => $e){
								if(is_array($e))
								{
									foreach($e as $f_name => $f){
										if(!array($f)){
											$this->_configFactory->saveConfig($b_name.'/'.$c_name.'/'.$d_name.'/'.$e_name.'/'.$f_name,$f,$scope,$scope_id);
										}
									}
								}else{
									$this->_configFactory->saveConfig($b_name.'/'.$c_name.'/'.$d_name.'/'.$e_name,$e,$scope,$scope_id);
								}
								
							}
						}else{
						$this->_configFactory->saveConfig($b_name.'/'.$c_name.'/'.$d_name,$d,$scope,$scope_id);	
						}
					}
					}
					
				}
			}
			
		foreach($this->_store as $store_id=>$store_code){
		
		$configCollection = $this->configCollection->create();
        $configCollection->addFieldToFilter("scope",['eq'=>$scope]);
		$configCollection->addFieldToFilter("scope_id",['eq'=>$store_id]);
		$configCollection->addFieldToFilter("path",['eq'=>"pocothemes/home/home_style"]);
       
		
		if($configCollection->count()>0){
            $poco_home_home_style = $configCollection->getFirstItem()->getData()['value'];
			$this->_configFactory->saveConfig('web/default/cms_home_page',$poco_home_home_style,$scope,$store_id);
        }
		
		
		}
		
		$menu_developer_mode = $this->helper->getConfigValue('optimize_performance','developer_mode_enable_disable','navigationmenu');		
		if(!$menu_developer_mode){
			$this->refreshNavigationMenuHtml();
		}
		
		//save home page style
		$poco_home_home_style = $this->helper->getHome('home_style',$scope_id);
		$this->pocoHelper->cachePrograme();
		$this->generateConfigCss();
		

            $gatewayResponse->setIsValid(true);
            $gatewayResponse->setRequestSuccess(true);

            if ($gatewayResponse->getIsValid()) {
                $gatewayResponse->setRequestMessage(__('Success to Import '.$demo_version.'.'));
				$this->pocoHelper->cachePrograme();
				
            } else {
                $gatewayResponse->setRequestMessage(__('Error during Import '.$demo_version.'.'));
            }
        } catch (\Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestMessage($exception->getMessage());
        }

        return $gatewayResponse;
    }
	public function refreshNavigationMenuHtml() {
		$dir = $this->_menu_helper->getStaticTemplateDirectoryPath();
        try {
            $files = glob($dir . "*"); // get all file names
            if (!empty($files)) {
                foreach ($files as $file) { // iterate files
                    if (is_file($file)) {
                        $result = unlink($file); // delete file
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, null, 'navigationmenu.log');
        }
    }
	
	
	public function generateConfigCss(){
		$baseUrl = $this->helper->getUrl();
		
		foreach($this->_store as $store_id=>$store_code){
			$baseUrl = $this->helper->getUrlWithoutStoreCode($store_id);
			$mediaUrl = $this->helper->getStoreMediaUrl($store_id);
			$dynamic_variable = '';
			
			// ----- Start General ------ //
			
			//Theme Color
			$dynamic_variable .= '@theme_border_color:#dddddd;'.PHP_EOL;
								
			//Typography
			$custom_font = $this->helper->getGeneral('custom_font',$store_id);
			if($custom_font){
				$main_font = $this->helper->getGeneral('font',$store_id);	
				$body_font_size = $this->helper->getGeneral('font_size',$store_id);				
				if($main_font != 'google_font'){
					if($main_font == "custom"){
						$body_font_family = $this->helper->getGeneral('custom_body_font',$store_id);
					}else{
						$body_font_family = $main_font;
					}
				} else {
					$main_google_font = $this->helper->getGeneral('google_font',$store_id);
					$namefontmain = explode(":",(string)str_replace("+", " ", $main_google_font)); 
					$body_font_family = $namefontmain[0];
				}

				$heading_font = $this->helper->getGeneral('heading_font',$store_id);	
				$heading_font_size = $this->helper->getGeneral('heading_font_size',$store_id);				$sub_heading_font_size = $this->helper->getGeneral('sub_heading_font_size',$store_id);
				
				if($heading_font == 'custom'){
					$heading_font_family = $this->helper->getGeneral('custom_heading_font',$store_id);
				}else if(($heading_font != 'google_font')&&($heading_font != 'custom')){
					$heading_font_family = $heading_font;
				} else {
					$heading_google_font = $this->helper->getGeneral('heading_google_font',$store_id);
					$namefontheader = explode(":",(string)str_replace("+", " ", $heading_google_font)); 
					$heading_font_family = $namefontheader[0];
				}


				//Element Google Font
				$google_font_element = $this->helper->getGeneral('google_font_targets',$store_id);
				$element_google_font_name = $this->helper->getGeneral('element_google_font',$store_id);
				$namegooglefont_element = explode(":",(string)str_replace("+", " ", $element_google_font_name));
				$element_googlefont = $namegooglefont_element[0];
				//end Element Google Font
				
				
			} else {
				$body_font_size = '14px';
				$heading_font_family = $body_font_family = 'Open Sans, Arial, sans-serif';
				$heading_font_size='30px';				
				$sub_heading_font_size='16px';
			}
			
			$dynamic_variable .= '@body_font_size:'.$body_font_size.';'.PHP_EOL;
			$dynamic_variable .= '@body_font_family:'.$body_font_family.';'.PHP_EOL;
			$dynamic_variable .= '@heading_font_family:'.$heading_font_family.';'.PHP_EOL;
			$dynamic_variable .= '@heading_font_size:'.$heading_font_size.';'.PHP_EOL;					
			$dynamic_variable .= '@sub_heading_font_size:'.$sub_heading_font_size.';'.PHP_EOL;			
			$loaderUrl = $this->helper->getLoadingIcon($store_id); 
			$dynamic_variable .= '@loaderUrl:"'.$loaderUrl.'";'.PHP_EOL;
			//Colors					
			$icon_move = '';	
			$mediaURL = $this->_storeManager->getStore($store_id)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);	
			$iconMove = $mediaURL.'pocothemes/images/icon-move.png';		
			$selectArw = $mediaURL.'pocothemes/images/select-arw.png';	
			$selectArwGry = $mediaURL.'pocothemes/images/arw-gry.png';
			$arrowPtrn = $mediaURL.'pocothemes/images/arrow-ptrn.png';	
			
			$refbtnbg = $mediaURL.'pocothemes/images/ref-btn-bg.png';	
			$birdIcon = $mediaURL.'pocothemes/images/bird_icon.png';
			$btnpbg = $mediaURL.'pocothemes/images/btn_pbg.png';	
			$arrwbg = $mediaURL.'pocothemes/images/arrw_bg.png';	
			$bnr_ofr_text = $mediaURL.'pocothemes/images/bnr_ofr_text.png';	
			$close_cursor = $mediaURL.'pocothemes/images/close_cursor.png';
			$empty_cart = $mediaURL.'pocothemes/images/empty-cart.png';
			$gmg_anm_img_1 = $mediaURL.'pocothemes/images/gmg_anm_img1.png';
			$gmg_anm_img_2 = $mediaURL.'pocothemes/images/gmg_anm_img2.png';
			$pettopbg = $mediaURL.'pocothemes/images/pet-top-bg-crv.png';
			$cwsarw_eot = $mediaURL.'pocothemes/fonts/cwsarw.eot';		
			$cwsarw_eot_iefix = $mediaURL.'pocothemes/fonts/cwsarw.eot?#iefix';	
			$cwsarw_woff = $mediaURL.'pocothemes/fonts/cwsarw.woff';			
			$cwsarw_ttf = $mediaURL.'pocothemes/fonts/cwsarw.ttf';			
			$cwsarw_svg = $mediaURL.'pocothemes/fonts/cwsarw.svg#cws-arrow';
			$dynamic_variable .= '@loaderUrl:"'.$loaderUrl.'";'.PHP_EOL;	
			$dynamic_variable .= '@iconMove:"'.$iconMove.'";'.PHP_EOL;	
			$dynamic_variable .= '@selectArw:"'.$selectArw.'";'.PHP_EOL;
			$dynamic_variable .= '@selectArwGry:"'.$selectArwGry.'";'.PHP_EOL;
			$dynamic_variable .= '@arrowPtrn:"'.$arrowPtrn.'";'.PHP_EOL;
			$dynamic_variable .= '@refbtnbg:"'.$refbtnbg.'";'.PHP_EOL;	
			$dynamic_variable .= '@birdIcon:"'.$birdIcon.'";'.PHP_EOL;	
			$dynamic_variable .= '@btnpbg:"'.$btnpbg.'";'.PHP_EOL;		
			$dynamic_variable .= '@arrwbg:"'.$arrwbg.'";'.PHP_EOL;		
			$dynamic_variable .= '@vt-bnr-offr-img:"'.$bnr_ofr_text.'";'.PHP_EOL;	
			$dynamic_variable .= '@close_cursor:"'.$close_cursor.'";'.PHP_EOL;		
			$dynamic_variable .= '@empty_cart:"'.$empty_cart.'";'.PHP_EOL;		
			$dynamic_variable .= '@gmg_anm_img_1:"'.$gmg_anm_img_1.'";'.PHP_EOL;
			$dynamic_variable .= '@gmg_anm_img_2:"'.$gmg_anm_img_2.'";'.PHP_EOL;		
			$dynamic_variable .= '@cwsarw_eot:"'.$cwsarw_eot.'";'.PHP_EOL;		
			$dynamic_variable .= '@cwsarw_eot_iefix:"'.$cwsarw_eot_iefix.'";'.PHP_EOL;	
			$dynamic_variable .= '@cwsarw_woff:"'.$cwsarw_woff.'";'.PHP_EOL;			
			$dynamic_variable .= '@cwsarw_ttf:"'.$cwsarw_ttf.'";'.PHP_EOL;		
			$dynamic_variable .= '@cwsarw_svg:"'.$cwsarw_svg.'";'.PHP_EOL;	
			$dynamic_variable .= '@pettopbg:"'.$pettopbg.'";'.PHP_EOL;			
			$custom_color = $this->helper->getGeneral('custom_color',$store_id);
			if($custom_color){
				$text_color = $this->helper->getGeneral('text_color',$store_id);
				$link_color = $this->helper->getGeneral('link_color',$store_id);
				$link_hover_color = $this->helper->getGeneral('hover_link_color',$store_id);
				$heading_text_color = $this->helper->getGeneral('heading_text_color',$store_id);
				$subheading_text_color = $this->helper->getGeneral('subheading_text_color',$store_id) ?? 'transparent';
				//Buttons
				$primary_btn_bg_color = $this->helper->getGeneral('primary_btn_bg_color',$store_id);
				$primary_btn_text_color = $this->helper->getGeneral('primary_btn_text_color',$store_id);
				$primary_btn_hover_bg_color = $this->helper->getGeneral('primary_btn_hover_bg_color',$store_id);
				$primary_btn_hover_text_color = $this->helper->getGeneral('primary_btn_hover_text_color',$store_id);
				$secondary_btn_bg_color = $this->helper->getGeneral('secondary_btn_bg_color',$store_id);
				$secondary_btn_text_color = $this->helper->getGeneral('secondary_btn_text_color',$store_id);
				$secondary_btn_hover_bg_color = $this->helper->getGeneral('secondary_btn_hover_bg_color',$store_id);
				$secondary_btn_hover_text_color = $this->helper->getGeneral('secondary_btn_hover_text_color',$store_id);
				
				//Breadcrumbs
				$breadcrumbs_bg_color = $this->helper->getGeneral('breadcrumbs_bg_color',$store_id);
				if(!$breadcrumbs_bg_color){
					$breadcrumbs_bg_color = 'transparent';
				}
				
				$breadcrumbs_text_color = $this->helper->getGeneral('breadcrumbs_text_color',$store_id);
				$breadcrumbs_links_color = $this->helper->getGeneral('breadcrumbs_links_color',$store_id);
				$breadcrumbs_links_hover_color = $this->helper->getGeneral('breadcrumbs_links_hover_color',$store_id);
				
				$addtocart_btn_text_color = $this->helper->getGeneral('addtocart_btn_text_color',$store_id);
				$addtocart_btn_bg_color = $this->helper->getGeneral('addtocart_btn_bg_color',$store_id);
				$addtocart_btn_border_color = $this->helper->getGeneral('addtocart_btn_border_color',$store_id);
				$addtocart_btn_hover_text_color = $this->helper->getGeneral('addtocart_btn_hover_text_color',$store_id);
				$addtocart_btn_hover_bg_color = $this->helper->getGeneral('addtocart_btn_hover_bg_color',$store_id);
				
				$other_btn_text_color = $this->helper->getGeneral('other_btn_text_color',$store_id);
				$other_btn_bg_color = $this->helper->getGeneral('other_btn_bg_color',$store_id);
				$other_btn_border_color = $this->helper->getGeneral('other_btn_border_color',$store_id);
				$other_btn_hover_text_color = $this->helper->getGeneral('other_btn_hover_text_color',$store_id);
				$other_btn_hover_bg_color = $this->helper->getGeneral('other_btn_hover_bg_color',$store_id);
				
				if(!$addtocart_btn_text_color) { $addtocart_btn_text_color = '#333'; }
				if(!$addtocart_btn_bg_color) { $addtocart_btn_bg_color = '#efefef'; }
				if(!$addtocart_btn_border_color) { $addtocart_btn_border_color = '#ddd'; }
				if(!$addtocart_btn_hover_text_color) { $addtocart_btn_hover_text_color = '#000'; }
				if(!$addtocart_btn_hover_bg_color) { $addtocart_btn_hover_bg_color = '#ddd'; }
				if(!$other_btn_text_color) { $other_btn_text_color = '#333'; }
				if(!$other_btn_bg_color) { $other_btn_bg_color = '#efefef'; }
				if(!$other_btn_border_color) { $other_btn_border_color = '#ddd'; }
				if(!$other_btn_hover_text_color) { $other_btn_hover_text_color = '#000'; }
				if(!$other_btn_hover_bg_color) { $other_btn_hover_bg_color = '#ddd'; }
				
				
				
			} else {
				$text_color = '#222222';
				$heading_text_color = '#222222';
				$subheading_text_color = '#222222';
				$link_color = '#222222';
				//$link_hover_color = $this->helper->getGeneral('theme_color',$store_id);
				$link_hover_color = '#222222';
				$this->helper->getGeneral('theme_color',$store_id);
				
				$primary_btn_bg_color = '#444444';
				$primary_btn_text_color = '#ffffff';
				$primary_btn_hover_bg_color = '#000000';
				$primary_btn_hover_text_color = '#ffffff';
				$secondary_btn_bg_color = '#666666';
				$secondary_btn_text_color = '#ffffff';
				$secondary_btn_hover_bg_color = '#222222';
				$secondary_btn_hover_text_color = '#ffffff';
				
				$breadcrumbs_bg_color = '#f0f0f0';
				$breadcrumbs_text_color = '#444444';
				$breadcrumbs_links_color = '#333333';
				$breadcrumbs_links_hover_color = '#000000';
				
				$addtocart_btn_text_color = '#333'; 
				$addtocart_btn_bg_color = '#efefef'; 
				$addtocart_btn_border_color = '#ddd'; 
				$addtocart_btn_hover_text_color = '#000'; 
				$addtocart_btn_hover_bg_color = '#ddd'; 
				$other_btn_text_color = '#333'; 
				$other_btn_bg_color = '#efefef'; 
				$other_btn_border_color = '#ddd';
				$other_btn_hover_text_color = '#000'; 
				$other_btn_hover_bg_color = '#ddd'; 
				
			}
			$dynamic_variable .= '@text_color:'.$text_color.';'.PHP_EOL;
			$dynamic_variable .= '@link_color:'.$link_color.';'.PHP_EOL;
			$dynamic_variable .= '@link_hover_color:'.$link_hover_color.';'.PHP_EOL;
			$dynamic_variable .= '@heading_text_color:'.$heading_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@subheading_text_color:'.$subheading_text_color.';'.PHP_EOL;
			
				
			$dynamic_variable .= '@primary_btn_bg_color:'.$primary_btn_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@primary_btn_text_color:'.$primary_btn_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@primary_btn_hover_bg_color:'.$primary_btn_hover_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@primary_btn_hover_text_color:'.$primary_btn_hover_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@secondary_btn_bg_color:'.$secondary_btn_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@secondary_btn_text_color:'.$secondary_btn_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@secondary_btn_hover_bg_color:'.$secondary_btn_hover_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@secondary_btn_hover_text_color:'.$secondary_btn_hover_text_color.';'.PHP_EOL;
			
			$dynamic_variable .= '@breadcrumbs_bg_color:'.$breadcrumbs_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@breadcrumbs_text_color:'.$breadcrumbs_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@breadcrumbs_links_color:'.$breadcrumbs_links_color.';'.PHP_EOL;
			$dynamic_variable .= '@breadcrumbs_links_hover_color:'.$breadcrumbs_links_hover_color.';'.PHP_EOL;
			
			$dynamic_variable .= '@addtocart_btn_text_color:'.$addtocart_btn_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@addtocart_btn_bg_color:'.$addtocart_btn_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@addtocart_btn_border_color:'.$addtocart_btn_border_color.';'.PHP_EOL;
			$dynamic_variable .= '@addtocart_btn_hover_text_color:'.$addtocart_btn_hover_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@addtocart_btn_hover_bg_color:'.$addtocart_btn_hover_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@other_btn_text_color:'.$other_btn_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@other_btn_bg_color:'.$other_btn_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@other_btn_border_color:'.$other_btn_border_color.';'.PHP_EOL;
			$dynamic_variable .= '@other_btn_hover_text_color:'.$other_btn_hover_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@other_btn_hover_bg_color:'.$other_btn_hover_bg_color.';'.PHP_EOL;
			
			//Page Wrapper
			$custom_page_wrapper = $this->helper->getGeneral('custom_page_wrapper',$store_id);
			if($custom_page_wrapper){
				$bg_color = $this->helper->getGeneral('bg_color',$store_id);
				$bg_image = $this->helper->getGeneral('bg_image',$store_id); 
				if($bg_image){
					//$page_wrapper_bg_path = $baseUrl.'pub/media/poco/background/'.$bg_image;
					//$page_wrapper_bg_path = $baseUrl.'media/poco/background/'.$bg_image;
					$page_wrapper_bg_path = $mediaUrl.'poco/background/'.$bg_image;
					$page_wrapper_background = 'background: url('.$page_wrapper_bg_path.');';	
					$dynamic_variable .= '@bg_image:'."'".$page_wrapper_bg_path."'".';'.PHP_EOL;
				}
				else
				{	
					$page_wrapper_background = '';
					$page_wrapper_bg_path = '';
					//$dynamic_variable .= '@bg_image:'."'".$page_wrapper_bg_path."'".';'.PHP_EOL;
					$dynamic_variable .= '@bg_image:none;'.PHP_EOL;			
					
				}
				if(!$bg_color){
					$bg_color = 'transparent';
				}
				$page_custom_style = $this->helper->getGeneral('page_custom_style',$store_id);
			} else {
				//$bg_color = '#ffffff';
				$bg_color = 'transparent';
				$page_custom_style = '';
				$page_wrapper_background = '';
				$page_wrapper_bg_path = '';
				$dynamic_variable .= '@bg_image:none;'.PHP_EOL;			
			}
			$page_wrapper =	'body { '.$page_wrapper_background.' background-color: '.$bg_color.';'.$page_custom_style.' }';
			
			//Main Content
			$custom_main_content = $this->helper->getGeneral('custom_main_content',$store_id);
			if($custom_main_content){
				$main_bgcolor = $this->helper->getGeneral('main_bgcolor',$store_id);
				if(!$main_bgcolor){
					$main_bgcolor = 'transparent';
				}
				$main_bg_image = $this->helper->getGeneral('main_bg_image',$store_id);
				if($main_bg_image){
					//$main_bg_path = $baseUrl.'pub/media/poco/background/'.$main_bg_image;
					//$main_bg_path = $baseUrl.'media/poco/background/'.$main_bg_image;
					$main_bg_path = $mediaUrl.'poco/background/'.$main_bg_image;
					$dynamic_variable .= '@main_bg_image:'."'".$main_bg_path."'".';'.PHP_EOL;
					$main_background = 'background: url('.$main_bg_path.');';
				}else{
					$main_background = '';
					$main_bg_path = '';
					//$dynamic_variable .= '@main_bg_image:'."'".$main_bg_path."'".';'.PHP_EOL;
					$dynamic_variable .= '@main_bg_image:none;'.PHP_EOL;
				}
				$main_custom_style = $this->helper->getGeneral('main_custom_style',$store_id);
			} else {
				$main_bgcolor = 'transparent';
				$main_background = '';
				$main_custom_style = '';
				$main_bg_path = '';
				//$dynamic_variable .= '@main_bg_image:'."'".$main_bg_path."'".';'.PHP_EOL;
				$dynamic_variable .= '@main_bg_image:none;'.PHP_EOL;
			}
			
			$maincontent =	'#maincontent { '.$main_background.' background-color: '.$main_bgcolor.';'.$main_custom_style.' }';
            $maincontent =	'.boxed-layout-poco .page-wrapper { '.$main_background.' background-color: '.$main_bgcolor.';'.$main_custom_style.' }';
			
			// ----- End General ------ //
			
			// ----- Start Theme Layout ----- //
			$layout_width = $this->helper->getThemeLayout('layout_width',$store_id);
			$layout_width = $this->helper->getThemeLayout('layout_width',$store_id);
			if($layout_width == "custom" ){
				$custom_width = $this->helper->getThemeLayout('custom_width',$store_id);	
			}else if($layout_width == "boxed" ){
				$custom_width = $this->helper->getThemeLayout('boxlayout_width',$store_id);	
			}else{
				$custom_width = '100%';
			}
			
			
			$rtl = $this->helper->getThemeLayout('rtl',$store_id);
			$scroll_top = $this->helper->getThemeLayout('scroll_top',$store_id);
			$scroll_position = $this->helper->getThemeLayout('scroll_position',$store_id);
			$home_padding = $this->helper->getThemeLayout('home_padding',$store_id);
			$home_bottom_margin = $this->helper->getThemeLayout('home_bottom_margin',$store_id);


			$dynamic_variable .= '@layout_width:'.$layout_width.';'.PHP_EOL;
			$dynamic_variable .= '@custom_width:'.$custom_width.';'.PHP_EOL;
			$dynamic_variable .= '@rtl:'.$rtl.';'.PHP_EOL;
			$dynamic_variable .= '@scroll_top:'.$scroll_top.';'.PHP_EOL;
			$dynamic_variable .= '@scroll_position:'.$scroll_position.';'.PHP_EOL;
			
			$dynamic_variable .= '@home_padding:'.$home_padding.';'.PHP_EOL;
			$dynamic_variable .= '@home_bottom_margin:'.$home_bottom_margin.';'.PHP_EOL;
			// ----- End Theme Layout ----- //
			
			// ----- Start Header ----- //
			//Header
			$custom_header = $this->helper->getHeader('custom_header',$store_id);
			$header_style = $this->helper->getHeader('header_style',$store_id);
			$header = '';
			if($custom_header){
				$header_bgcolor = $this->helper->getHeader('bgcolor',$store_id);
				if($header_bgcolor){
					$dynamic_variable .= '@header_bgcolor:'.$header_bgcolor.';'.PHP_EOL;
					$header_bgcolor_l = 'background-color: @header_bgcolor;';
				} else {
					//$header_bgcolor_l = '#ffffff';
					$header_bgcolor_l = 'background-color:transparent;';
					$header_bgcolor = 'transparent';
					$dynamic_variable .= '@header_bgcolor:'.$header_bgcolor.';'.PHP_EOL;
				}
				$header_bg_image = $this->helper->getHeader('bg_image',$store_id);
				
				if($header_bg_image){
					//$header_bg_path = $baseUrl.'pub/media/poco/background/'.$header_bg_image;
					//$header_bg_path = $baseUrl.'media/poco/background/'.$header_bg_image;
					$header_bg_path = $mediaUrl.'poco/background/'.$header_bg_image;
					
					$header_background = 'background: url('.$header_bg_path.');';
				}else{
					$header_background = '';
					$header_bg_image = '';
					$dynamic_variable .= '@header_bg_image:none;'.PHP_EOL;
				}
				//$dynamic_variable .= '@header_bg_image:'."'".$header_bg_image."'".';'.PHP_EOL;
				
				
				$header_bordercolor = $this->helper->getHeader('bordercolor',$store_id);
				if($header_bordercolor){
					$dynamic_variable .= '@header_bordercolor:'.$header_bordercolor.';'.PHP_EOL;
					$header_bordercolor_l = 'border-color: @header_bordercolor;';
				} else {
					$dynamic_variable .= '@header_bordercolor:transparent;'.PHP_EOL;
					$header_bordercolor_l = '';
				}
						
				$header_textcolor = $this->helper->getHeader('textcolor',$store_id);
				if($header_textcolor){
					$dynamic_variable .= '@header_textcolor:'.$header_textcolor.';'.PHP_EOL;
					$header_textcolor_l = 'color: @header_textcolor;';
				} else {
					$header_textcolor_l = '';
				}
				$header .= '.'.$header_style.'{ '.$header_bgcolor_l.$header_background.$header_bordercolor_l.$header_textcolor_l.' }';
				//Top Links
				$top_links_bgcolor = $this->helper->getHeader('top_links_bgcolor',$store_id);
				$top_links_color = $this->helper->getHeader('top_links_color',$store_id);
				$top_links_hover_color = $this->helper->getHeader('top_links_hover_color',$store_id);
				
				//Searchbox
				$search_bgcolor = $this->helper->getHeader('search_bgcolor',$store_id);
				$search_text_color = $this->helper->getHeader('search_text_color',$store_id);
				$search_bordercolor = $this->helper->getHeader('search_bordercolor',$store_id);
				
				//Mini Cart
				$minicart_bgcolor = $this->helper->getHeader('minicart_bgcolor',$store_id);
				$minicart_color = $this->helper->getHeader('minicart_color',$store_id);
				$minicart_icon_color = $this->helper->getHeader('minicart_icon_color',$store_id);
				
				
			} else {
				$header_bgcolor = '#ffffff';
				$header_background = '';
				$header_bordercolor = '#dddddd';
				$header_textcolor = '#222222';
				
				$top_links_bgcolor = '#7e807e';
				$top_links_color = '#ffffff';
				//$top_links_hover_color = $theme_color;
				$top_links_hover_color = '#222222';
				
				$search_bgcolor = '#ffffff';
				$search_text_color = '#222222';
				$search_bordercolor = '#f0f0f0';
				
				$minicart_bgcolor = '';
				$minicart_color = '#222';
				$minicart_icon_color = '#222';
				
				$dynamic_variable .= '@header_bgcolor:'.$header_bgcolor.';'.PHP_EOL;
				$dynamic_variable .= '@header_bordercolor:'.$header_bordercolor.';'.PHP_EOL;
				$dynamic_variable .= '@header_textcolor:'.$header_textcolor.';'.PHP_EOL;
				$header .= '.'.$header_style.'{ background-color:@header_bgcolor; border-color: @header_bordercolor; color: @header_textcolor; 395395}';
			}
			$sticky_menu = $this->helper->getHeader('sticky_menu_bgcolor',$store_id);
			if($sticky_menu){
				$sticky_menu_bgcolor = $this->helper->getHeader('sticky_menu_bgcolor',$store_id)?$this->helper->getHeader('sticky_menu_bgcolor',$store_id):'rgba(255,255,255,0.89)';
			}else{
				$sticky_menu_bgcolor = 'rgba(255,255,255,0.89)';
			}
			$dynamic_variable .= '@sticky_menu_bgcolor:'.$sticky_menu_bgcolor.';'.PHP_EOL;
			//$dynamic_variable .= '@sticky_menu_opacity:'.$sticky_menu_opacity.';'.PHP_EOL;
			
			//Top Links
			$dynamic_variable .= '@top_links_bgcolor:'.$top_links_bgcolor.';'.PHP_EOL;
			$dynamic_variable .= '@top_links_color:'.$top_links_color.';'.PHP_EOL;
			$dynamic_variable .= '@top_links_hover_color:'.$top_links_hover_color.';'.PHP_EOL;
				
			//Searchbox
			$dynamic_variable .= '@search_bgcolor:'.$search_bgcolor.';'.PHP_EOL;
			$dynamic_variable .= '@search_text_color:'.$search_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@search_bordercolor:'.$search_bordercolor.';'.PHP_EOL;
			
			//Mini Cart
			$dynamic_variable .= '@minicart_bgcolor:'.$minicart_bgcolor.';'.PHP_EOL;
			$dynamic_variable .= '@minicart_color:'.$minicart_color.';'.PHP_EOL;
			$dynamic_variable .= '@minicart_icon_color:'.$minicart_icon_color.';'.PHP_EOL;

			//Top Bar Header
			$top_header = $this->helper->getHeader('top_header',$store_id);
			if($top_header){
				$top_header_bgcolor = $this->helper->getHeader('top_header_bgcolor',$store_id);
				$top_header_text_color = $this->helper->getHeader('top_header_text_color',$store_id);
				
			}else{
				$top_header_bgcolor = '#ffffff';
				$top_header_text_color = '#222222';
			}
			$dynamic_variable .= '@top_header_bgcolor:'.$top_header_bgcolor.';'.PHP_EOL;
			$dynamic_variable .= '@top_header_text_color:'.$top_header_text_color.';'.PHP_EOL;
			// ----- End Header ----- //
			
			// ----- Start Home Page ----- //
			$home_slider_width_style = $this->helper->getHome('home_slider_width',$store_id);
			if($home_slider_width_style == "box"){
				$home_slider_width = $this->helper->getHome('slider_box_width_size',$store_id);
			}else{
				$home_slider_width = '100%';
			}
			$dynamic_variable .= '@home_slider_width:'.$home_slider_width.';'.PHP_EOL;
			
			//$brand_slider = $this->helper->getHome('brand_slider',$store_id);
			//if($brand_slider){
				$brand_slider_bgcolor = $this->helper->getHome('brand_slider_bgcolor',$store_id);
			/*}else{
				$brand_slider_bgcolor = '#ffffff';
			}*/
			if($brand_slider_bgcolor){
					$dynamic_variable .= '@brand_slider_bgcolor:'.$brand_slider_bgcolor.';'.PHP_EOL;
			}else{
				$dynamic_variable .= '@brand_slider_bgcolor:none;'.PHP_EOL;
			}
			
			
			$testimonial_background = '';
			$testimonial_textcolor = $this->helper->getHome('testimonial_textcolor',$store_id);
			$testimonial_bgcolor = $this->helper->getHome('testimonial_bgcolor',$store_id);
			$testimonial_bgimage = $this->helper->getHome('testimonial_bgimage',$store_id);
			if($testimonial_bgimage){
				//$testimonial_bg_path = $baseUrl.'pub/media/poco/background/'.$testimonial_bgimage;
				//$testimonial_bg_path = $baseUrl.'media/poco/background/'.$testimonial_bgimage;
				$testimonial_bg_path = $mediaUrl.'poco/background/'.$testimonial_bgimage;
				
				$testimonial_background = '.testimonial-slider-bgimg{background: url('.$testimonial_bg_path.');}';
			}
			if($testimonial_textcolor){
				$dynamic_variable .= '@testimonial_textcolor:'.$testimonial_textcolor.';'.PHP_EOL;
			}else{
				$dynamic_variable .= '@testimonial_textcolor:none;'.PHP_EOL;
			}
			if($testimonial_bgcolor){
				$dynamic_variable .= '@testimonial_bgcolor:'.$testimonial_bgcolor.';'.PHP_EOL;
			}else{
				$dynamic_variable .= '@testimonial_bgcolor:none;'.PHP_EOL;
			}
					
			// ----- End Home Page ----- //
			
			// ----- Start Footer ----- //
			$custom_footer = $this->helper->getFooter('custom_footer',$store_id);
			$footer_style = $this->helper->getFooter('footer_style',$store_id);
			$footer = '';
			if($custom_footer){
				$footer_bgcolor = $this->helper->getFooter('bgcolor',$store_id);
				if($footer_bgcolor){
					$dynamic_variable .= '@footer_bgcolor:'.$footer_bgcolor.';'.PHP_EOL;
					$footer_bgcolor_l = 'background-color: @footer_bgcolor;';
				} else {
					$footer_bgcolor_l = '';
				}
				
				$footer_bg_image = $this->helper->getFooter('bg_image',$store_id);
				if($footer_bg_image){
					//$footer_bg_path = $baseUrl.'pub/media/poco/background/'.$footer_bg_image;
					//$footer_bg_path = $baseUrl.'media/poco/background/'.$footer_bg_image;
					$footer_bg_path = $mediaUrl.'poco/background/'.$footer_bg_image;
					
					//$footer_background = 'background: url('.$footer_bg_path.');';
					$footer_background = 'background: url('.$footer_bg_path.') !important;';
					$dynamic_variable .= '@footer_bg_image:'."url(".$footer_bg_path.")".';'.PHP_EOL;
					
				}else{
					$footer_background = '';
					$dynamic_variable .= '@footer_bg_image:'.'none;'.PHP_EOL;
					//$dynamic_variable .= '@footer_bg_image:'."url(".$footer_bg_path.")".';'.PHP_EOL;
				}
				//$dynamic_variable .= '@footer_bg_image:'."'".$footer_bg_image."'".';'.PHP_EOL;
				
				$footer_bordercolor = $this->helper->getFooter('bordercolor',$store_id);
				if($footer_bordercolor){
					$dynamic_variable .= '@footer_bordercolor:'.$footer_bordercolor.';'.PHP_EOL;
					$footer_bordercolor_l = 'border-color: @footer_bordercolor;';
				} else {
					$footer_bordercolor_l = '';
				}
						
				$footer_textcolor = $this->helper->getFooter('textcolor',$store_id);
				if($footer_textcolor){
					$dynamic_variable .= '@footer_textcolor:'.$footer_textcolor.';'.PHP_EOL;
					$footer_textcolor_l = 'color: @footer_textcolor;';
				} else {
					$footer_textcolor_l = '';
				}
				$footer_heading_text_color = $this->helper->getFooter('heading_text_color',$store_id);
				if($footer_heading_text_color){
					$dynamic_variable .= '@ftr_heading_text_color:'.$footer_heading_text_color.';'.PHP_EOL;
					$footer_heading_text_color = 'color: @ftr_heading_text_color;';
				} else {
					$footer_heading_text_color = '';
				}
				
				$footer_font_size = $this->helper->getFooter('font_size',$store_id);
				if($footer_font_size){
					$dynamic_variable .= '@footer_font_size:'.$footer_font_size.';'.PHP_EOL;
					$footer_font_size_l = 'font-size: @footer_font_size;';
				} else {
					$footer_font_size_l = 'font-size: 14px;';
				}
				
				$footer .= '.'.$footer_style.'{ '.$footer_bgcolor_l.$footer_background.$footer_bordercolor_l.$footer_textcolor_l.$footer_font_size_l.' }';
				
				//Footer Links
				$footer_links_color = $this->helper->getFooter('footer_links_color',$store_id);
				$footer_link_hover_color = $this->helper->getFooter('footer_link_hover_color',$store_id);
				
			} else {
				$footer_bgcolor = '#efefef';
				$footer_bordercolor = '#dddddd';
				$footer_textcolor = '#444444';
				
				$footer_links_color = '#000000';
				$footer_link_hover_color = '#555555';
				
				$dynamic_variable .= '@footer_bgcolor:'.$footer_bgcolor.';'.PHP_EOL;
				$dynamic_variable .= '@footer_bordercolor:'.$footer_bordercolor.';'.PHP_EOL;
				$dynamic_variable .= '@footer_textcolor:'.$footer_textcolor.';'.PHP_EOL;
				$footer .= '.'.$footer_style.'{ background-color:@footer_bgcolor; border-color: @footer_bordercolor; color: @footer_textcolor; }';
			}
			
			//Footer Links
			$dynamic_variable .= '@footer_links_color:'.$footer_links_color.';'.PHP_EOL;
			$dynamic_variable .= '@footer_link_hover_color:'.$footer_link_hover_color.';'.PHP_EOL;
			
			// ----- End Footer ----- //
			
			// Cookie Policy Popup
			$enable_cookie = $this->helper->getConfigValue('cookie_policy','enable_cookie_policy','pocothemes',$store_id);
			if($enable_cookie){
				$bar_bg_color = $this->helper->getConfigValue('cookie_policy','bar_background_color','pocothemes',$store_id);
				$bar_height = $this->helper->getConfigValue('cookie_policy','bar_height','pocothemes',$store_id);
				$text_font_size = $this->helper->getConfigValue('cookie_policy','text_font_size','pocothemes',$store_id);
				$read_more_btn_color = $this->helper->getConfigValue('cookie_policy','read_more_btn_color','pocothemes',$store_id);
				$read_more_text_color = $this->helper->getConfigValue('cookie_policy','read_more_text_color','pocothemes',$store_id);
				$agree_btn_color = $this->helper->getConfigValue('cookie_policy','agree_btn_color','pocothemes',$store_id);
				$agree_text_color = $this->helper->getConfigValue('cookie_policy','agree_text_color','pocothemes',$store_id);
			} else {
				$bar_bg_color = '#333';
				$bar_height = '28px';
				$text_font_size = '12px';
				$read_more_btn_color = '#000000';
				$read_more_text_color= '#222222';
				$agree_btn_color = '#000000';
				$agree_text_color = '#222222';
			}
			

			$dynamic_variable .= '@cookie_bar_bg_color:'.$bar_bg_color.';'.PHP_EOL;
			$dynamic_variable .= '@cookie_bar_height:'.$bar_height.';'.PHP_EOL;
			$dynamic_variable .= '@cookie_text_font_size:'.$text_font_size.';'.PHP_EOL;
			$dynamic_variable .= '@cookie_read_more_btn_color:'.$read_more_btn_color.';'.PHP_EOL;
			
			$dynamic_variable .= '@cookie_read_more_text_color:'.$read_more_text_color.';'.PHP_EOL;
			$dynamic_variable .= '@cookie_agree_btn_color:'.$agree_btn_color.';'.PHP_EOL;
			$dynamic_variable .= '@cookie_agree_text_color:'.$agree_text_color.';'.PHP_EOL;
				
			// Page Wrapper
			$dynamic_variable .= '@wrapper_bg_color:'.$bg_color.';'.PHP_EOL;
			 	
			//Main Content
			$dynamic_variable .= '@main_bgcolor:'.$main_bgcolor.';'.PHP_EOL;
		
			
			//Page Wrapper
			$dynamic_variable .= $page_wrapper.PHP_EOL;
			
			//Main Content
			$dynamic_variable .= $maincontent.PHP_EOL;
			
			//Header
			$dynamic_variable .= $header.PHP_EOL;
			
			//Home
			if($testimonial_background){
				$dynamic_variable .= $testimonial_background.PHP_EOL;
			}
			
			//Footer
			$dynamic_variable .= $footer.PHP_EOL;
			
			//----- End assign dynamic variable values

			if($rtl){
				$ex_rtl = '_rtl';
			} else {
				$ex_rtl = '';
			}
						
			$config_file = $this->helper->getViewDirectory().'/frontend/web/css/_config.less';
			$config_less = file_get_contents($config_file);

			//print_r($dynamic_variable);exit;
			$content = $dynamic_variable.$config_less;
			//print_r($content);exit;		
			$file_name = "poco_config".$store_code.".css";
			//$less_file_name = "poco_config".$store_code.".less";
			//$config_out_file = $this->helper->getViewDirectory().'/frontend/web/css/'.$less_file_name;	
			
			//file_put_contents( $config_out_file, $content );	
			$mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath().'pocothemes/';
			
			if(!$this->file->isDirectory($mediaPath)) {
				//mkdir($mediaPath);
				$this->file->createDirectory($mediaPath);
				}
			$css_file_name = "poco_config".$store_code.".css";

			$path_css = $mediaPath.$css_file_name;
			
			$parser = new \Less_Parser(['relativeUrls' => false,'compress' => true]);
			gc_disable();
			$parser->parse($content);
			$group_dynamic_css_content = $parser->getCss();
			gc_enable();
			$css_file = $mediaPath.$store_code."-google-fonts.css";
			if ( file_exists($css_file) ) {
					unlink($css_file);
			}
			$css_file_final = $mediaPath.$store_code."-google-fonts-final.css";
			if ( file_exists($css_file_final)) {
					unlink($css_file_final);
			}
			$font_FolderPath = $mediaPath.$store_code.'_fonts';
			if (file_exists($font_FolderPath)) {
				$this->delTree($font_FolderPath);
			}
			$googleFontLib = $this->getGoogleFont($mediaPath,$store_code,$store_id);
			// Remove comments
			 $googleFontLib = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $googleFontLib);
			 // Remove spaces before and after selectors, braces, and colons
			 $googleFontLib = preg_replace('/\s*([{}|:;,])\s+/', '$1', $googleFontLib);
			 // Remove remaining spaces and line breaks
			 $googleFontLib = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '',$googleFontLib);
			 
			$this->file->filePutContents($path_css,$group_dynamic_css_content);
		}
	}
	public function getGoogleFont($mediaPath,$store_code,$store_id){
		$generalConfig = $this->helper->getConfigGroup('general',$store_id);	
			$main_font = array_key_exists('font', $generalConfig) ? $generalConfig['font'] : null;
			$main_google_font = array_key_exists('google_font', $generalConfig) ? $generalConfig['google_font'] : null;
			$heading_font = array_key_exists('heading_font', $generalConfig) ? $generalConfig['heading_font'] : null;
			$heading_google_font = array_key_exists('heading_google_font', $generalConfig) ? $generalConfig['heading_google_font'] : null;
			$google_font_element = array_key_exists('google_font_targets', $generalConfig) ? $generalConfig['google_font_targets'] : null;
			$element_google_font_name = array_key_exists('element_google_font', $generalConfig) ? $generalConfig['element_google_font'] : null;
			$fonts = array();
			if($main_font == 'google_font')	{
				$fonts[] = $main_google_font;
			}
			if ($heading_font == 'google_font' ) {
				$fonts[] = $heading_google_font;
			}
			if ($google_font_element != '') {
				$fonts[] = $element_google_font_name;
			}
			$downLoadGoogleFont = array_unique($fonts);
			$google_fonts = implode("|", array_unique($fonts));
			if($main_font == 'google_font' || $heading_font == 'google_font' || $google_font_element != ''):
			$googleLiveFont = null;
			$css_file = $mediaPath.$store_code."-google-fonts.css";
			if ( file_exists($css_file) ) {
					unlink($css_file);
			}
			$css_file_final = $mediaPath.$store_code."-google-fonts-final.css";
			if ( file_exists($css_file_final) ) {
					unlink($css_file_final);
			}
	
			foreach($downLoadGoogleFont as $key => $googlefont):
				$googlefont = str_replace(' ', '%20', $googlefont);
				$googleFontPath = 'https://fonts.googleapis.com/css?family='.$googlefont;
				
				$font_FolderPath = $mediaPath.$store_code.'_fonts';
				if ( file_exists($font_FolderPath) ) {
						//$this->delTree($font_FolderPath);
				}
			$dynamic_google_Css = $this->downloadGoogleFont($googleFontPath,$store_code,$mediaPath,$store_id);
			endforeach;
			$css_file_final = $mediaPath.$store_code."-google-fonts-final.css";
		$dynamic_google_Css = str_replace( 'src:', 'font-display: swap;
			src:', $dynamic_google_Css );
			
			$this->file->filePutContents($css_file_final,$dynamic_google_Css,FILE_APPEND);
			return $dynamic_google_Css;
			endif;
			
			
	
	}
	public function downloadGoogleFont($link,$store_code,$mediaPath,$store_id)
	{
		$regex_url = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		// Check if there is a url in the text
		if( preg_match( $regex_url, $link, $url ) ) {
			$css_file = $mediaPath.$store_code."-google-fonts.css";
			
			$google_css = $url[0];
			$google_css = rtrim( $google_css, "'" );
			$ch = curl_init();
			$fp = fopen ( $css_file, 'a+' );
			$ch = curl_init( $google_css );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 50 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );		
			$mediaUrl = $this->helper->getStoreMediaUrl($store_id);
			$font_FolderURL = $mediaUrl.'pocothemes/'.$store_code.'_fonts';
			$font_Folder = $store_code.'_fonts';
			$font_FolderPath = $mediaPath.$store_code.'_fonts';
			// fonts folder operations. Delete if exists. Then create a new
			if ( file_exists($font_FolderPath) ) {
			//	$this->delTree($font_FolderPath);
			}else{
				mkdir($font_FolderPath);	
			}
			// get the download css content
			$css_file_contents = file_get_contents($css_file);
			
			if (!str_contains($css_file_contents, 'font-display: swap')) {
				$css_file_contents = str_replace('src:','font-display: swap;
	src:' , $css_file_contents);
				file_put_contents($css_file,$css_file_contents);
			}
			
			
			
			if ( preg_match_all( $regex_url, $css_file_contents, $fonts ) ) {
			$fonts = $fonts[0];
			
			foreach ( $fonts as $i => $font ) {
				$font = rtrim( $font, ")" );

				$font_file = explode( "/", $font );
				$font_file = array_pop( $font_file );

				// download font
				$ch = curl_init();
				$fpFont = fopen ( $font_FolderPath."/{$font_file}", 'a+' );
				$ch = curl_init( $font );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 50 );
				curl_setopt( $ch, CURLOPT_FILE, $fpFont );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
				curl_exec( $ch );
				curl_close( $ch );
				fclose( $fpFont );				
				$css_file_contents = str_replace( $font, $font_FolderURL."/{$font_file}", $css_file_contents );
			}
			/*$fh = fopen ( $css_file, 'a+' );
			fwrite( $fh, $css_file_contents );
			fclose( $fh );*/
		}
		return $css_file_contents;
		}
	}
	
	function delTree( $dir ) { 
	$files = array_diff( scandir( $dir ), array( '.', '..' ) ); 
	foreach ( $files as $file ) { 
		( is_dir( "$dir/$file" ) && !is_link( $dir ) ) ? delTree( "$dir/$file" ) : unlink( "$dir/$file" ); 
	} 

	return rmdir($dir); 
}
}
