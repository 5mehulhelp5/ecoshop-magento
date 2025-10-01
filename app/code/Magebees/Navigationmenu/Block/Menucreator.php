<?php
namespace Magebees\Navigationmenu\Block;
class Menucreator extends \Magento\Framework\View\Element\Template
{
    protected $group_option = '';
    protected $_config;
    protected $_optimizeconfig;
    protected $customerSession = '';
    protected $menustaticitem;
    protected $menucreatorgroup;
	protected $storeManager;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
		\Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatorgroup,
		\Magento\Customer\Model\SessionFactory $customerSession,
		\Magebees\Navigationmenu\Model\Menustaticitem $menustaticitem,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
     	$this->menucreatorgroup = $menucreatorgroup;
		$this->menustaticitem = $menustaticitem;
		$this->customerSession = $customerSession;
		$this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    public function getConfig()
    {
           $this->_config = $this->_scopeConfig->getValue('navigationmenu/general', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
           return $this->_config;
    }
	public function getCssPath($menu_type,$groupId){
		$_config = $this->getConfig();
		$cssurl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."magebees/navigationmenu/css/".$menu_type."-".$groupId.".css";
		 
			if($_config['root_point_pub'])
			{
				 $cssurl = str_replace("pub/","",$cssurl);
				 
			}		
		return $cssurl;
		
	}
    public function getOptimizeConfig()
    {
            $this->_optimizeconfig = $this->_scopeConfig->getValue('navigationmenu/optimize_performance', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return $this->_optimizeconfig;
    }
      //for posh theme
    public function getIdFromUniqueCode($unique_code){
        $group_id = $this->menucreatorgroup->load($unique_code,'unique_code')->getId();
        return $group_id;
    }
    //for posh theme  
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getMenutype($group_id)
    {
		$group_details = $this->menucreatorgroup->load($group_id);
        $group_menutype = trim($group_details->getMenutype());
        return $group_menutype;
    }
    public function getMenuStatus($group_id)
    {
		$group_details = $this->menucreatorgroup->load($group_id);
        $group_status = trim($group_details->getStatus());
        return $group_status;
    }
	public function getCurrentCustomerDetails(){
			$customer_detail = array();
		
			if($this->customerSession->create()->isLoggedIn()){
        	$customer_detail['customerGroupId'] = $this->customerSession->create()->getCustomer()->getGroupId();
			$customer_detail['customerStatus'] = 'login';
		}else{
			$customer_detail['customerGroupId'] = 0;
			$customer_detail['customerStatus'] = 'guest';
		}
		return $customer_detail;
	}
	public function getCurrentStoreDetails(){
		$store_detail = array();
		$store_detail['storeId'] = $this->storeManager->getStore()->getStoreId();
        $store_detail['storeCode'] =$this->storeManager->getStore()->getCode();
        $store_detail['website_id'] = $this->storeManager->getWebsite()->getId();
		return $store_detail;
	}
    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
	public function generateStaticHtml($groupId,$website_id,$store_id,$storeCode,$customerStatus,$customerGroupId)
	{
        $group_option = '';
        $group_details = $this->menucreatorgroup->load($groupId);
        if ($group_details->getStatus() == "1") {
            $group_position = $group_details->getPosition();
            $group_menutype = $group_details->getMenutype();
            if (($group_menutype == 'mega-menu')) {
                $group_option = $group_menutype." ".$group_position;
            } else {
                $group_option = $group_menutype;
            }
            $group_level = $group_details->getLevel();
            $direction = $group_details->getDirection();
        
			$theme_option_rtl = $this->_scopeConfig->getValue('posh/theme_layout/rtl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
            if($direction=='rtl' && $theme_option_rtl){
                $direction_css = 'rtl';
            } else {
                $direction_css = 'ltr';
            }
			$_config = $this->getConfig();
			$responsive_breakpoint = $_config['responsive_break_point'];	
			if(isset($_config['responsive_break_point']))
			{
				$responsive_breakpoint = $_config['responsive_break_point'];
			}else
			{
				$responsive_breakpoint = '767px';	
			}
			$menu_data_init = array();
			$menu_data = array();
			$menu_data['group_id'] = '#cwsMenu-'.$groupId;
			$menu_data['menu_type'] = $group_menutype;
			$menu_data['responsive_breakpoint'] = $responsive_breakpoint;
			$menu_data_init['mb_menu_init'] = $menu_data;
			
			if ($group_menutype == 'list-item') {
                if ($direction_css!='') {
                    $menufront = "<div id='cwsMenu-".$groupId."' class='".$direction_css."'>";
                } else {
                    $menufront = "<div id='cwsMenu-".$groupId."'>";
                }
            } elseif ($group_menutype == 'mega-menu') {			
			
			    if ($direction_css!='') {
                    $menufront = "<div id='cwsMenu-".$groupId."' class='cwsMenuOuter ".$group_position." ".$direction_css."' data-mage-init='".json_encode($menu_data_init)."'>";
                } else {
                    $menufront = "<div id='cwsMenu-".$groupId."' class='cwsMenuOuter' data-mage-init='".json_encode($menu_data_init)."'>";
                }
            } else {
                if ($direction_css!='') {
                    $menufront = "<div id='cwsMenu-".$groupId."' class='cwsMenuOuter ".$direction_css."' data-mage-init='".json_encode($menu_data_init)."'>";
                } else {
                    $menufront = "<div id='cwsMenu-".$groupId."' class='cwsMenuOuter' data-mage-init='".json_encode($menu_data_init)."'>";
                }
            }
        
            if ($group_details->getShowhidetitle() == "2") {
                $menufront .= '<div class="h3 menuTitle">'.$group_details->getTitle().'</div>';
            }
            if ($group_menutype != 'list-item') {
                $menufront .="<ul class='cwsMenu ".$group_option."'>";
            } else {
                $menufront .="<ul>";
            }
             $menufront .= $this->menustaticitem->getStaticMenuContent($groupId,$website_id,$store_id,$storeCode,$customerStatus,$customerGroupId);
			
            $menufront .= "</ul>";
            $menufront .= "</div>";
            return $menufront;
        } else {
            return;
        }
    }
}
