<?php
namespace Magebees\Ajaxcomparewishlist\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Url;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
	protected $_scopeConfig;
	protected $assetRepo;
	protected $request;
	protected $jsonHelper;
	
	
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Repository $assetRepo,
        RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
		$this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function isAjaxCompareEnabled()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/general/enabledcompare', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getPopupMsg()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/general/comparemsg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getPopupTime()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/general/popuptime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function isAjaxWishEnabled()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/ajaxwishgeneral/enabledwish', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getWishMsg()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/ajaxwishgeneral/wishmessage', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	public function getPopupWishTime()
    {
        return $this->_scopeConfig->getValue('magebees_ajaxcomparewishlist/ajaxwishgeneral/popupwishtime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	public function getWidgetConfigurationOptions()
    {
        return $this->jsonHelper->jsonEncode($this->getConfigurationOptions());
    }
	public function getWishListUrl()
    {
		$use_store = $this->_scopeConfig->getValue('web/url/use_store', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);	
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		if($use_store):
			$storeCode = $this->_storeManager->getStore()->getCode();
			return $baseUrl.$storeCode.'/wishlist';
		else:
			return $baseUrl.'wishlist';
		endif;
	}
    public function getConfigurationOptions()
    {
        return [
            'isShowSpinner' => 1,
            'isShowSuccessMessage' => 1,
            'successMessageText' => $this->getWishMsg(),
            'customerLoginUrl' => $this->_urlBuilder->getUrl(Url::ROUTE_ACCOUNT_LOGIN),
            'popupTtl' => $this->getPopupWishTime()
        ];
    }
}