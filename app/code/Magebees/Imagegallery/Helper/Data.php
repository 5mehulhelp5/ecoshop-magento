<?php
namespace Magebees\Imagegallery\Helper;
use Magento\Framework\App\ObjectManager;
class Data extends \Magento\Framework\Url\Helper\Data
{
	const IMAGEGALLERY_ENABLE = 'imagegallery/general/enabled';
	const ALLOWED_IMAGEGALLERY_TYPE = 'imagegallery/general/allow_file_type';
	const SIDEBAR_CONFIGURATION = 'imagegallery/sidebar';
	protected $_request;
	protected $_storeManager;
	protected $ImagegalleryFactory;
	protected $_scopeConfig;
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magebees\Imagegallery\Model\ImagegalleryFactory $ImagegalleryFactory
	) {
		$this->_request = $request;
		$this->_storeManager = $storeManager; 
		$this->ImagegalleryFactory = $ImagegalleryFactory;
		$this->_scopeConfig = $scopeConfig;
		parent::__construct($context);
    }
	
	public function isEnableImagegallery()
	{
		
		$imagegallery_enable = $this->scopeConfig->getValue(
            self::IMAGEGALLERY_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $imagegallery_enable;
	}
	public function sidebarConfiguration(){
		
		$sidebar_configuration = $this->scopeConfig->getValue(
            self::SIDEBAR_CONFIGURATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $sidebar_configuration;
	}
	public function allowedFiletype()
	{
		
		$allowed_file_type = $this->scopeConfig->getValue(
            self::ALLOWED_IMAGEGALLERY_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		
		$allowed_file_type_arr = explode(",",$allowed_file_type);
		return $allowed_file_type_arr;
	}
	
	public function getStoreId()
	{
		return $this->_storeManager->getStore()->getId();
	}
	public function getImageUrl($image)
    {
		$url = false;
        //$image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $url = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . 'imagegallery' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
	public function getUrl($path)
    {
		
        if ($path) {
            if (is_string($path)) {
				
				$use_store = $this->_scopeConfig->getValue('web/url/use_store', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);	
				$baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
				if($use_store):
					$storeCode = $this->_storeManager->getStore()->getCode();
					$url = $baseUrl.$storeCode.$path;
				else:
					$url = $baseUrl.$path;
				endif;
			} else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the url.')
                );
            }
        }
        return $url;
    }
	 public function getGalleyImageCollection($limit=null) {
        $storeId = $this->getStoreId();
		$collection = $this->ImagegalleryFactory->create()->getCollection();
		$collection->addFieldToFilter('status', array('eq' => 1));
		$collection->addFieldToFilter(['stores', 'stores'], [['finset' => 0], ['finset' => $storeId]]);
		$collection->setOrder('sort_order', 'ASC');
		if($limit):
			$collection->setPageSize($limit);
		endif;
		return $collection;
    }
	
	
}
