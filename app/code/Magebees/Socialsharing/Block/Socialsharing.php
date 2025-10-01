<?php
namespace Magebees\Socialsharing\Block;

use Magento\Cms\Block\Page;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Socialsharing\Helper\Data as HelperData;
use Magebees\Socialsharing\Model\System\Config\Source\Btnsize;
use Magebees\Socialsharing\Model\System\Config\Source\Menu;
use Magebees\Socialsharing\Model\System\Config\Source\Floatapplyfor;
use Magebees\Socialsharing\Model\System\Config\Source\Floatposition;
use Magebees\Socialsharing\Model\System\Config\Source\Style;
use Magento\Store\Model\StoreManagerInterface;

class Socialsharing extends Template
{
	const SERVICES        = ['whatsapp','facebook','twitter','pinterest','linkedin', 'tumblr'];
    const CLICK_FULL_MENU = '2';
    const CLICK_MENU      = '1';
    const HOVER_MENU      = '0';

    protected $_helperData;
    protected $_page;
	protected $storeManager;
	protected $_urlInterface;

    public function __construct(
        Context $context,
        HelperData $helperData,
        Page $page,
		UrlInterface $urlInterface,
		StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_page = $page;
		$this->storeManager = $storeManager;
		$this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }
	
	/* General Configuration */
	
    public function isEnable()
    {	
        return $this->_helperData->isEnabled();
    }
	public function getShareCounter()
    {
        if ($this->_helperData->isShareCounter()) {
            return "a2a_counter";
        }
        return "";
    }
	public function getThankYou()
    {
        if ($this->_helperData->isThankYouPopup()) {
            return "true";
        }
        return "false";
    }
	public function isShowUnderCart()
    {
        return $this->_helperData->isShowUnderCart();
    }
	public function getButtonSize()
    {
        $type = $this->getData('type');
        if ($type == 'inline') {
            $inlineSize = $this->_helperData->getInlineButtonSize();
            return $this->setButtonSize($inlineSize);
        }
		$floatSize = $this->_helperData->getButtonSizeDesktop();
        return $this->setButtonSize($floatSize);
    }
	
	public function getFlotDesktopButtonSize()
    {
        $type = $this->getData('type');
		if($type == "float"){
			$floatSize = $this->_helperData->getButtonSizeDesktop();
			return $this->setButtonSize($floatSize);
		}
	}
	public function getFlotMobileButtonSize()
    {
        $type = $this->getData('type');
		if ($type == 'inline') {
            $inlineSize = $this->_helperData->getInlineButtonSize();
            return $this->setButtonSize($inlineSize);
        }
		$floatSize = $this->_helperData->getButtonSizeMobile();
		return $this->setButtonSize($floatSize);
	}
	
    public function setImageSize($buttonSize)
    {
        switch ($buttonSize) {
            case "a2a_kit_size_16":
                return 'width="16" height="16"';
                break;
            case "a2a_kit_size_32":
                return 'width="32" height="32"';
                break;
            case "a2a_kit_size_64":
                return 'width="64" height="64"';
                break;
            default:
                return 'width="32" height="32"';
                break;
        }
    }
	
    public function setButtonSize($buttonSize)
    {
        switch ($buttonSize) {
            case Btnsize::SMALL:
                return "a2a_kit_size_16";
                break;
            case Btnsize::MEDIUM:
                return "a2a_kit_size_32";
                break;
            case Btnsize::LARGE:
                return "a2a_kit_size_64";
                break;
            default:
                return "a2a_kit_size_32";
                break;
        }
    }
	
	/* Color And Style Configuration */
	public function getIconColor()
    {
        return $this->_helperData->getIconColor();
    }
	
    public function getButtonColor()
    {
        return $this->_helperData->getButtonColor();
    }
    public function getBackgroundColor()
    {
        $color = $this->_helperData->getBackgroundColor();
        return "background: " . $color . ";";
    }
	public function getFloatStyle()
    {
        if (!$this->isDisplayInline()) {
            $floatStyle = $this->_helperData->getFloatStyle();
            if ($floatStyle == Style::VERTICAL) {
                return "a2a_vertical_style";
            }
            return "a2a_default_style";
        }
        return null;
    }
	public function isVerticalStyle($floatStyle)
    {
        return $floatStyle == "a2a_vertical_style";
    }

    public function getFloatPosition()
    {
        $floatPosition = $this->_helperData->getFloatPosition();
        if ($floatPosition == FloatPosition::LEFT) {
            return "left: 0px;";
        }
        return "right: 0px;";
    }
	
    public function getFloatMargin($type)
    {
        if ($type == "bottom") {
            $floatMarginBottom = $this->_helperData->getFloatMarginBottom();
            return "bottom: " . $floatMarginBottom . "px;";
        }
        $floatMarginTop = $this->_helperData->getFloatMarginTop();
        return "top: " . $floatMarginTop . "px;";
    }
	/*
	public function getButtonSizeDesktop()
    {
		return $this->_helperData->getButtonSizeDesktop();
    }
	public function getButtonSizeMobile()
    {
		return $this->_helperData->getButtonSizeMobile();
    }
	*/
	public function getHideondevice()
    {
		return $this->_helperData->getHideondevice();
    }
	public function getDeviceMaxWidth()
    {
		return $this->_helperData->getDeviceMaxWidth();
    }
	
	/* Social Share Configuration  */
	
	public function getEnableService()
    {
        $enableServices = [];
        foreach (self::SERVICES as $service) {
            if ($this->_helperData->isServiceEnable($service)) {
                array_push($enableServices, $service);
            }
        }
        return $enableServices;
    }
    public function getImageUrl($service)
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $modulePath = 'magebees/socialsharing/';
        $imageUrl = null;
        $imageUrl = $baseUrl . $modulePath . $service . '/' . $this->_helperData->getServiceImage($service);

        return $imageUrl;
    }
	public function isAddMoreShare()
    {
        return $this->_helperData->isAddMoreShare();
    }
	
    public function isImageEnable($service)
    {
        return $this->_helperData->getServiceImage($service) != null;
    }
	
    public function getDisabledServices()
    {
        $disabledServices = [];
        foreach (self::SERVICES as $service) {
            if ($this->_helperData->getDisableService($service) != null) {
                array_push($disabledServices, $this->_helperData->getDisableService($service));
            }
        }
        return implode(",", $disabledServices);
    }

    public function getNumberOfService()
    {
        return $this->_helperData->getNumberOfServices();
    }

    public function getMenuType()
    {
        $menuType = $this->_helperData->getDisplayMenu();
        if ($menuType == Menu::ON_CLICK) {
            if ($this->_helperData->isFullMenuOnClick()) {
                return self::CLICK_FULL_MENU;
            }
            return self::CLICK_MENU;
        }
        return self::HOVER_MENU;
    }
	
    public function getDisplayType()
    {
        $type = $this->getData('type');
        if ($type == 'float') {
            return 'a2a_floating_style mbSocialShareFloat';
        }
        if ($type == 'inline') {
            return 'a2a_default_style';
        }
        return null;
    }
	
    public function isDisplayInline()
    {
        $type = $this->getData('type');
        return $type == 'inline';
    }

    public function getContainerClass($displayType)
    {
        $position = $this->getData('position');
        if ($displayType == 'a2a_default_style') {
            if ($position == 'under_cart') {
                return "mbSocialShareInlineUnderCart";
            }
            return "mbSocialShareInline";
        }
        return null;
    }
	
	/* Pages Configuration */

    public function isThisPageEnable()
    {
        $type = $this->getData('type');
		$thisPage = $this->getData('page');
		$allowPages = array();
        if ($type == 'inline') {
			if($this->isShowUnderCart() == 1){
				return true;
			}
        }
        if ($type == 'float') {
            if ($this->_helperData->getFloatApplyPages() == FloatApplyFor::ALL_PAGES) {
                return true;
            }
            if ($this->_helperData->getFloatApplyPages() == FloatApplyFor::SELECT_PAGES) {
                $selectPages = explode(',', $this->_helperData->getFloatSelectPages());
                $cmsPages = explode(',', $this->_helperData->getFloatCmsPages());
                if ($thisPage == "cms_page") {
                    $pageId = $this->_page->getPage()->getId();
                    if (in_array($pageId, $cmsPages)) {
                        return true;
                    }
                }
                if (in_array($thisPage, $selectPages)) {
                    return true;
                }
				$UrlArray = $this->getFloatPageLinks();
				if(!empty($UrlArray)){
					$currURL = $this->getCurrentUrl();
					if(in_array($currURL,$UrlArray)){
						return true;
					}
				}
            }
        }
        return false;
    }
	
    public function isThisPositionEnable()
    {
        $thisPosition = $this->getData('position');
        $positionArray = [];
        if ($thisPosition == "float_position") {
            return true;
        }
        if ($this->isShowUnderCart()) {
            array_push($positionArray, "under_cart");
        }
        if (in_array($thisPosition, $positionArray)) {
            return true;
        }
        return false;
    }
	public function getCurrentUrl()
    {
		$currentUrl = (string)$this->_urlInterface->getCurrentUrl();
		return rtrim($currentUrl,'/');
    }
	public function getFloatPageLinks()
    {
		$urlarray = explode("\n",$this->_helperData->getFloatPageLinks());
		$urlarray = array_map('trim', $urlarray);
		return $urlarray;
    }
}