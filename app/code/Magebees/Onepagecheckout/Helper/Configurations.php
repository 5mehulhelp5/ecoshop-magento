<?php
namespace Magebees\Onepagecheckout\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

class Configurations extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
	protected $scopeConfig;
    protected $backendConfig;
    protected $isArea = [];
    protected $objectManager;
	protected $_json;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Serialize\Serializer\Json $json,
        ObjectManagerInterface $objectManager
    ) {
		$this->_storeManager = $storeManager;
		$this->_json = $json;
        $this->objectManager = $objectManager;
        parent::__construct($context);
	}
	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	public function getTitleFontColors(){
		return $this->getConfig('magebees_Onepagecheckout/style_management/title_font_colors');
	}
	public function getTitleBgColors(){
		return $this->getConfig('magebees_Onepagecheckout/style_management/title_bg_colors');
	}
	
	public function getButtonFontColors(){
		return $this->getConfig('magebees_Onepagecheckout/style_management/button_font_colors');
	}
	public function getButtonBgColors(){
		return $this->getConfig('magebees_Onepagecheckout/style_management/button_bg_colors');
	}
	public function getEnable(){
		return (bool)$this->getConfig('magebees_Onepagecheckout/general/enable_in_frontend');
    }
	public function redirectCheckoutAfterAddProduct(){
		return (boolean)$this->getConfig('magebees_Onepagecheckout/general/redirect_checkout');
	}
	public function getCheckoutTitle(){
		return $this->getConfig('magebees_Onepagecheckout/general/checkout_title');
	}
	public function getCheckoutDescription(){
		return $this->getConfig('magebees_Onepagecheckout/general/checkout_description');
	}
	public function getDefaultCountry(){
		return $this->getConfig('magebees_Onepagecheckout/general/country_id');
	}
	public function getDefaultRegionId(){
		return $this->getConfig('magebees_Onepagecheckout/general/region_id');
	}
	public function getDefaultCity(){
		return $this->getConfig('magebees_Onepagecheckout/general/default_city');
	}
	public function getDefaultPostalCode(){
		return $this->getConfig('magebees_Onepagecheckout/general/default_postal_code');
	}
	public function getSignInSettings(){
		return $this->getConfig('magebees_Onepagecheckout/osp_checkout_mode/show_login_link');
	}
	public function getLoginMsg(){
		return $this->getConfig('magebees_Onepagecheckout/osp_checkout_mode/login_link_title');
	}
	public function enableDifferentBillingAddress(){
		return (boolean)$this->getConfig('magebees_Onepagecheckout/general/billing_different_address');
	}
	
	public function enableNewsletter(){
		return (boolean)$this->getConfig('magebees_Onepagecheckout/general/enabled_newsletter');
	}
	
	public function newsletterChecked(){
		return (boolean)$this->getConfig('magebees_Onepagecheckout/general/default_newsletter');
	}
	
	public function newsletterLabel(){
		return $this->getConfig('magebees_Onepagecheckout/general/label_newsletter');
	}
	
	public function getFullRequest()
    {
        $routeName = $this->_getRequest()->getRouteName();
        $controllerName = $this->_getRequest()->getControllerName();
        $actionName = $this->_getRequest()->getActionName();
        return $routeName.'_'.$controllerName.'_'.$actionName;
    }
	
	public function getAddressFieldsJsonConfig()
    {
		return $this->_json->serialize($this->getAddressFieldsConfig());
    }
	public function getAddressFieldsConfig()
    {
		$configs = array();
        $configs['twoFields'] = array();
        $configs['oneFields'] = array('street.0','street.1','street.2','street.3');
        $configs['lastFields'] = array();
        $configs['position'] = array();
        for($position = 0; $position < 20; $position++){
            $prePos = $position - 1;
            $currentPos = $position;
            $nextPos = $position + 1;

            $prepath = 'field_position_management/row_'.$prePos;
            $path = 'field_position_management/row_'.$currentPos;
            $nextpath = 'field_position_management/row_'.$nextPos;
			$preField = $this->getConfig('magebees_Onepagecheckout/'.$prepath);
            $currentField = $this->getConfig('magebees_Onepagecheckout/'.$path);
            $nextField = $this->getConfig('magebees_Onepagecheckout/'.$nextpath);

            if($currentField != '0'){
                if($currentField == 'street'){
                    $configs['position']['street'] = $currentPos;
                    $configs['position']['street.0'] = $currentPos;
                    $configs['position']['street.1'] = $currentPos;
                    $configs['position']['street.2'] = $currentPos;
                    $configs['position']['street.3'] = $currentPos;
                }elseif($currentField == 'region_id'){
                    $configs['position']['region_id'] = $currentPos;
                    $configs['position']['region'] = $currentPos;
                }else{
                    $configs['position'][$currentField] = $currentPos;
                }
            }
            if($currentField != 'street' && $currentField != '0'){
                if( $currentPos%2 == 0){
                    if($currentField != '0' && $nextField == '0'){
                        $configs['oneFields'][] = $currentField;
                        if($currentField == 'region_id'){
                            $configs['oneFields'][] = 'region';
                        }
                    }else{
                        $configs['twoFields'][] = $currentField;
                        if($currentField == 'region_id'){
                            $configs['twoFields'][] = 'region';
                        }
                    }
                }else{
                    if($currentField != '0' && $preField == '0'){
                        $configs['oneFields'][] = $currentField;
                        if($currentField == 'region_id'){
                            $configs['oneFields'][] = 'region';
                        }
                    }else{
                        $configs['twoFields'][] = $currentField;
                        $configs['lastFields'][] = $currentField;
                        if($currentField == 'region_id'){
                            $configs['twoFields'][] = 'region';
                            $configs['lastFields'][] = 'region';
                        }
                    }
                }
            }
        }
        return $configs;
    }

    public function isEnableStaticBlock()
    {
        return $this->getConfig('magebees_Onepagecheckout/block_configuration/is_enabled_block');
    }

    public function getStaticBlockList($stores = null)
    {
        $serialize = $this->objectManager->create('Magento\Framework\Serialize\Serializer\Json');
        return $serialize->unserialize($this->getConfig('magebees_Onepagecheckout/block_configuration/list'));
    }
	
	 public function getFieldSortOrder($fieldKey){
        $config = $this->getAddressFieldsConfig();
        if(isset($config['position']) && isset($config['position'][$fieldKey])){
            return $config['position'][$fieldKey];
        }
        return false;
    }
	
	public function getDefaultShippingMethod()
    {
        return $this->getConfig('magebees_Onepagecheckout/general/default_shipping_method');
    }
}