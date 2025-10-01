<?php
namespace Magebees\EmailEditor\Helper;
use Magento\Framework\Filesystem;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $productMetadata;
    protected $productFactory;
    protected $_categoryFactory;
	 public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
    \Magento\Framework\App\ProductMetadataInterface $productMetadata) {        
        $this->_storeManager = $storeManager;  	
        $this->productMetadata = $productMetadata; 
         $this->productFactory = $productFactory; 
          $this->_categoryFactory = $categoryFactory;   
	    parent::__construct($context);
    }
    public function getCategoryMenu()
    {
        $categoryId=2;
        $cat_arr=[];
        $root_category=$this->_categoryFactory->create()->load($categoryId);
        $subcategories = $root_category->getChildren();
        foreach(explode(',',(string)$subcategories) as $subcategory) {
        $category = $this->_categoryFactory->create()->load($subcategory);
        $cat_arr[$category->getId()]=$category;      
        }

        return array_reverse($cat_arr);
    }
	public function getConfig($field,$storeId = null)
    
    {      
    //'emaileditor/setting ';
         return $this->scopeConfig->getValue($field,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        );
    }
     public function loadProduct($productId)
    {
        $product=$this->productFactory->create()->load($productId);
        return $product;
    }  
     public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
     public function getLightLogoImageUrl()
    {
        $image=$this->getConfig('emaileditor/light_logo/image');
        $image_url=$this->_storeManager->getStore()
               ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'emaileditor/lightlogo/'.$image;
        return $image_url;
    }
    public function getDarkLogoImageUrl()
    {
        $image=$this->getConfig('emaileditor/dark_logo/image');
        $image_url=$this->_storeManager->getStore()
               ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'emaileditor/darklogo/'.$image;
        return $image_url;
    }
   
	 
   
}