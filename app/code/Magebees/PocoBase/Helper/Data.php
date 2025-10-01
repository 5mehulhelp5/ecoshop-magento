<?php
namespace Magebees\PocoBase\Helper;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $aclRetriever;
	protected $authSession;
	protected $typeListInterface;
	protected $pool;
	protected $themeModel;
	protected $menucreatorgroup;
	protected $_storeManager;
	protected $reader;
	protected $_file;
	protected $_menu_helper;
	protected $_slider_helper;
	protected $responsivebannerslider;
	protected $CriticalCss;
	
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Theme\Model\Theme $themeModel,
        \Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatorgroup,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
		\Magento\Backend\Model\Auth\Session $authSession,
       	TypeListInterface $typeListInterface,
		Pool $pool,
		\Magento\Framework\Module\Dir\Reader $reader,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magebees\Navigationmenu\Helper\Data $menu_helper,
		\Magebees\Responsivebannerslider\Helper\Data $slider_helper,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magebees\PocoThemes\Model\CriticalCss $CriticalCss
    ) {
		$this->aclRetriever = $aclRetriever;
		$this->authSession = $authSession;
		$this->typeListInterface = $typeListInterface;
		$this->pool = $pool;
		$this->themeModel = $themeModel;
		$this->menucreatorgroup = $menucreatorgroup;
		$this->_storeManager = $storeManager;
		$this->reader = $reader;
		$this->_file = $file;
		$this->_menu_helper = $menu_helper;
		$this->_slider_helper = $slider_helper;
		$this->responsivebannerslider = $responsivebannerslider;
		$this->CriticalCss = $CriticalCss;	
		parent::__construct($context);
	}
	
	public function getStoreId(){
		return $this->_storeManager->getStore()->getId();
	}

	public function cachePrograme()
	{
		$_cacheTypeList = $this->typeListInterface;
		$_cacheFrontendPool = $this->pool;

		$types = array('config','full_page');
				
		foreach ($types as $type) 
		{
			$_cacheTypeList->cleanType($type);
		}
		
		foreach ($_cacheFrontendPool as $cacheFrontend) 
		{
			$cacheFrontend->getBackend()->clean();
		}
	}
	
	public function getUserRole(){
    	$user = $this->authSession->getUser();
		$role = $user->getRole();
		$resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
		if($role->getRoleName()=="Pocodemo"){
			return false;
		}
		return true;
    }

    public function getThemeCode($store_id){
    	$theme_id = $this->scopeConfig->getValue('design/theme/theme_id',ScopeInterface::SCOPE_STORE,$store_id);
   		return $this->themeModel->load($theme_id)->getCode();
    }
	
	
	public function generateDynamicCssBanner(){
		/*Start Working to  Create & Update the Dynamic Css file for the menu items*/
		$bamner_slider_data = $this->responsivebannerslider->getCollection();
		
		$cssDir= '/pub/media/responsivebannerslider';

		$cssDir = $this->_slider_helper->getStaticCssDirectoryPath($cssDir);
		
		$dir_permission = 0755;
		$file_permission = 0644; 
				
		if(!$this->_file->isExists($cssDir))
		{
			$this->_file->createDirectory($cssDir,$dir_permission);
		}
		if(!$this->_file->isWritable($cssDir))
		{
			$this->_file->changePermissionsRecursively($cssDir,$dir_permission,$file_permission);
		}
		
		
				
		foreach($bamner_slider_data as $slidergroup){
		$path = $cssDir.'/';
		$path .= "group-".$slidergroup->getData('slidergroup_id').".css";
		$css = $this->get_slider_css($slidergroup->getData('slidergroup_id'));
		
		
			$unique_code = $slidergroup->getData('unique_code');
				$criticalCssCollection = $this->CriticalCss->getCollection()
								 ->addFieldToFilter('stores', $unique_code)
								 ->addFieldToFilter('type', 'slider_group');
								 if($criticalCssCollection->getSize()>0):
									 $criticalCssCollection->walk('delete');
								 endif;
								 $criticalCssInfo = array();
								 $criticalCssInfo['stores'] = $unique_code;
								 $criticalCssInfo['type'] = 'slider_group';
								 $criticalCssInfo['css'] = $css;
								 $this->CriticalCss->setData($criticalCssInfo)->save();
								 
		file_put_contents($path,$css);	
		
		
		}
		/*End Working to  Create & Update the Dynamic Css file for the slider items*/
	}
	public function get_slider_css($group_id){
		$groupdata = $this->responsivebannerslider->load($group_id);
		$max_width = $groupdata->getMaxWidth();
		$content_background = $groupdata->getContentBackground();
		$banner_content_tcolor = $groupdata->getBannerContentTcolor();
		$content_opacity = $groupdata->getContentOpacity();
		$navigation_acolor = $groupdata->getNavigationAcolor();
		$pagination_color = $groupdata->getPaginationColor();
		$pagination_bocolor = $groupdata->getPaginationBocolor();
		$pagination_active_color = $groupdata->getPaginationActive();
		$pagination_bar = $groupdata->getPaginationBar();
		$pagination_bocolor = $groupdata->getPaginationBocolor();
		if ($max_width > 0) {
			$max_width = $groupdata->getMaxWidth().'px';
		} else {
			$max_width = "";
		}
		$css = '';
		$css .= '#cwsslider-'.$group_id.' { }';
		$css .= '#cwsslider-'.$group_id.' { max-width:'.$max_width.'; }';
		$css .= '#cwsslider-'.$group_id.' .sliderdecs { background-color:'.$content_background.'; opacity:0.'.$content_opacity.'; color:'.$banner_content_tcolor.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-arw > div:before { color:'.$navigation_acolor.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet { background-color:'.$pagination_color.' !important; border-color:'.$pagination_bocolor.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-progressbar .swiper-pagination-progressbar-fill { background-color:'.$pagination_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet.swiper-pagination-bullet-active { background-color:'.$pagination_active_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet:hover { background-color:'.$pagination_active_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-arw > div { background-color:'.$pagination_bar.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-fraction { color:'.$pagination_color.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-fraction .swiper-pagination-current { color:'.$pagination_active_color.'; }';
		return $css;
	}
	
	public function generateDynamicCssMenu(){
		/*Start Working to  Create & Update the Dynamic Css file for the menu items*/
		$menugroup_data = $this->menucreatorgroup->getCollection();
		$moduleviewDir=$this->reader->getModuleDir('view', 'Magebees_Navigationmenu');
		$cssDir=$moduleviewDir.'/frontend/web/css/navigationmenu';
	
		$dir_permission = 0755;
		$file_permission = 0664;
	
		if(!$this->_file->isExists($cssDir))
		{
			$this->_file->createDirectory($cssDir,$dir_permission);
		}
		if(!$this->_file->isWritable($cssDir))
		{
			$this->_file->changePermissionsRecursively($cssDir,$dir_permission,$file_permission);
		}
	
		//$path_less = $cssDir.'/';
		$path_css = $this->_menu_helper->getDynamicCSSDirectoryPath();
		if(!$this->_file->isExists($path_css))
		{
			$this->_file->createDirectory($path_css,$dir_permission);
		}
		if(!$this->_file->isWritable($path_css))
		{
			$this->_file->changePermissionsRecursively($path_css,$dir_permission,$file_permission);
		}
		
		foreach($menugroup_data as $menugroup){
			$menu_type = $menugroup->getData('menutype');
			$unique_code = $menugroup->getData('unique_code');
			
			
			$group_id = $menugroup->getId();
			
			
				$css_less = $this->get_less_variable($group_id);
				$file_name = $path_css.$menu_type."-".$group_id.".css";
				if($menu_type=="mega-menu")
				{
						$master_less_file = $cssDir.'/'.'master-mega-menu.php';
						$master_less = file_get_contents($master_less_file);
						$content = $css_less.$master_less;
					
						$parser = new \Less_Parser(['relativeUrls' => false,'compress' => true]);
						 gc_disable();
						$parser->parse($content);
						$group_dynamic_css_content = $parser->getCss();
						gc_enable();
						
						file_put_contents($file_name,$group_dynamic_css_content);
						$criticalCssCollection = $this->CriticalCss->getCollection() ->addFieldToFilter('stores', $unique_code)->addFieldToFilter('type', 'menu_group');	
								if($criticalCssCollection->getSize()>0):	
								$criticalCssCollection->walk('delete');
								endif;						
								$criticalCssInfo = array();	
								$criticalCssInfo['stores'] = $unique_code;	
								$criticalCssInfo['type'] = 'menu_group';	
								$criticalCssInfo['css'] = $group_dynamic_css_content;	
								$this->CriticalCss->setData($criticalCssInfo)->save();
								
				}elseif(($menu_type=="smart-expand")||($menu_type=="always-expand")){
						$master_less_file = $cssDir.'/'.'master-expand-menu.php';
						$master_less = file_get_contents($master_less_file);
						$content = $css_less.$master_less;
						$parser = new \Less_Parser(['relativeUrls' => false,'compress' => true]);
						 gc_disable();
						$parser->parse($content);
						$group_dynamic_css_content = $parser->getCss();
						gc_enable();
						
						file_put_contents($file_name,$group_dynamic_css_content);
						$criticalCssCollection = $this->CriticalCss->getCollection()->addFieldToFilter('stores', $unique_code)->addFieldToFilter('type', 'menu_group');
								if($criticalCssCollection->getSize()>0):		
								$criticalCssCollection->walk('delete');	
								endif;							
								$criticalCssInfo = array();	
								$criticalCssInfo['stores'] = $unique_code;	
								$criticalCssInfo['type'] = 'menu_group';
								$criticalCssInfo['css'] = $group_dynamic_css_content;
								$this->CriticalCss->setData($criticalCssInfo)->save();
								
				}
			
		}
		
		/*End Working to  Create & Update the Dynamic Css file for the menu items*/
	}


	public function get_less_variable($group_id)
	{
		$groupdata = $this->menucreatorgroup->load($group_id);
		$scope_info = $this->scopeConfig->getValue('navigationmenu/general',ScopeInterface::SCOPE_STORE);
		$responsive_breakpoint = '';
		$dynamic_variable = '';
		$dynamic_variable = '@group_id:'.$group_id.';'.PHP_EOL;
		$itemimageheight = $groupdata->getImageHeight().'px';
		$dynamic_variable .= '@itemimageheight:'.$itemimageheight.';'.PHP_EOL;
		$itemimagewidth = $groupdata->getImageWidth().'px';
		$dynamic_variable .= '@itemimagewidth:'.$itemimagewidth.';'.PHP_EOL;
		
		if(isset($scope_info['responsive_break_point']))
		{
		$dynamic_variable .= '@responsive_breakpoint:'.$scope_info['responsive_break_point'].';'.PHP_EOL;	
		}else{
			$dynamic_variable .= '@responsive_breakpoint:767px;'.PHP_EOL;
		}
		
		
		$informations = $groupdata->getData();
			foreach($informations as $key => $value):
					if($this->isJSON($value)){
					$sub_information = json_decode($value, true);
					foreach($sub_information as $subkey => $subvalue):
						if($subvalue==""){
							
							$textColor = array('titlecolor','lvl0color','lvl0colorh','lvl0colora','mmlvl1color','mmlvl1colorh','mmlvl1colora','mmlvl2color','mmlvl2colorh', 'mmlvl2colora','mmlvl3color','mmlvl3colorh','mmlvl3colora','sublvl1color','sublvl1colorh','sublvl1colora','sublvl2color','sublvl2colorh', 'sublvl2colora','sublvl3color','sublvl3colorh','sublvl3colora','ddlinkcolor','ddlinkcolorh','ddlinkcolora');
							
							$bgColor = array('titlebgcolor','menubgcolor','lvl0bgcolor','lvl0bgcolorh','lvl0bgcolora','mmpnl-bgcolor','mmlvl1bgcolor','mmlvl1bgcolorh','mmlvl1bgcolora','mmlvl2bgcolor','mmlvl2bgcolorh','mmlvl2bgcolora','mmlvl3bgcolor','mmlvl3bgcolorh','mmlvl3bgcolora','sublvl1bgcolor','sublvl1bgcolorh','sublvl1bgcolora','sublvl2bgcolor','sublvl2bgcolorh','sublvl2bgcolora','sublvl3bgcolor','sublvl3bgcolorh','sublvl3bgcolora', 'ddpnl-bgcolor', 'ddlinkbgcolor','ddlinkbgcolorh','ddlinkbgcolora');
							
							$dividerColor = array('lvl0dvcolor','mmpnl-bdcolor','mmlvl1dvcolor','mmlvl2dvcolor','mmlvl3dvcolor','sublvl1dvcolor','sublvl2dvcolor','sublvl3dvcolor','ddpnl-bdcolor','ddlinkdvcolor');
							
							if (in_array($subkey, $textColor))
							{
								$subvalue = '#111111';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}else if (in_array($subkey, $bgColor)){
								$subvalue = 'transparent';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}else if (in_array($subkey, $dividerColor)){
								$subvalue = '#E1E1E1';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}
							$dynamic_variable .= '@'.$subkey.':;'.PHP_EOL;	
						}else{
							$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
						}
					endforeach;
					}
			endforeach;
		return $dynamic_variable;
	}

	public function isJSON($string)
	{
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}


}