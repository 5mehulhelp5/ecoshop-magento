<?php
namespace Magebees\Socialsharing\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $_storeManager;
	protected $scopeConfig;
    protected $backendConfig;
	protected $isArea = [];
    protected $objectManager;
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    ) {
		$this->_storeManager = $storeManager;
        $this->objectManager = $objectManager;
        parent::__construct($context);
	}
	 
	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    } 
	
	/* General Configuration */
	public function isEnabled()
    {
		return $this->getConfig('mbsocialsharing/general/enabled');
    }
	public function isShareCounter()
    {
        return $this->getConfig('mbsocialsharing/general/sharecounter');
    }
	public function isThankYouPopup()
    {
        return $this->getConfig('mbsocialsharing/general/thankyou');
    }
	public function isShowUnderCart()
    {
		return $this->getConfig('mbsocialsharing/general/show_under_cart');
    }
	public function getInlineButtonSize()
    {
		return $this->getConfig('mbsocialsharing/general/btnsize');
    }
	
	
	/* Color And Style Configuration */
	public function getIconColor()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/iconcolor');
    }
	public function getButtonColor()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/btncolor');
    }

    public function getBackgroundColor()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/bgcolor');
	}
	public function getFloatStyle()
    {
        return $this->getConfig('mbsocialsharing/colorstyle/style');
    }
	public function getFloatPosition()
    {
        return $this->getConfig('mbsocialsharing/colorstyle/position');
    }

    public function getFloatMarginTop()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/margin_top');
    }

    public function getFloatMarginBottom()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/margin_bottom');
    }
	
    public function getButtonSizeDesktop()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/btnsizedesktop');
    }
	public function getButtonSizeMobile()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/btnsizemobile');
    }
	public function getHideondevice()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/hideondevice');
    }
	public function getDeviceMaxWidth()
    {
		return $this->getConfig('mbsocialsharing/colorstyle/devicemaxwidth');
    }
	
	/* Social Share Configuration  */
	public function isServiceEnable($serviceCode)
    {
		return $this->getConfig('mbsocialsharing/socialservice/'.$serviceCode.'/enabled');
    }
	
    public function getServiceImage($serviceCode)
    {
		return $this->getConfig('mbsocialsharing/socialservice/'.$serviceCode.'/image');
    }
	
    public function isAddMoreShare()
    {
		return $this->getConfig('mbsocialsharing/socialservice/add_more_share/enabled');
    }
	
    public function getDisplayMenu()
    {
		return $this->getConfig('mbsocialsharing/socialservice/add_more_share/display_menu');
    }
	
    public function getNumberOfServices()
    {
		return $this->getConfig('mbsocialsharing/socialservice/add_more_share/number_service');
    }
	
    public function isFullMenuOnClick()
    {
		return $this->getConfig('mbsocialsharing/socialservice/add_more_share/full_menu');
    }
	
    public function getDisableService($serviceCode, $storeId = null)
    {
        if (!$this->isServiceEnable($serviceCode, $storeId)) {
            return $serviceCode;
        }
        return null;
    }
	
	/* Pages Configuration */
	
	/*
	public function getInlineApplyPages()
    {
		return $this->getConfig('mbsocialsharing/inline/apply_for');
    }
    public function getInlinePosition()
    {
		return $this->getConfig('mbsocialsharing/inline/position');
    }*/
	
	public function getFloatApplyPages()
    {
		return $this->getConfig('mbsocialsharing/float/apply_for');
    }
    public function getFloatSelectPages()
    {
		return $this->getConfig('mbsocialsharing/float/select_page');
    }
    public function getFloatCmsPages()
    {
		return $this->getConfig('mbsocialsharing/float/cms_page');
    }
	public function getFloatPageLinks()
    {
		return $this->getConfig('mbsocialsharing/float/pagelinks');
    }
	
	/* Extra */
    public function getBorderRadius()
    {
        return $this->getConfig('mbsocialsharing/general/borderradius');
    }
}