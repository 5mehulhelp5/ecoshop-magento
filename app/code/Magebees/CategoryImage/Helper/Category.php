<?php

namespace Magebees\CategoryImage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Category extends AbstractHelper
{
    protected $categoryFactory = null;
   
	
	/**
     * @return array
     */
    public function getAdditionalImageTypes()
    {
        return array('thumbnail');
    }

    /**
     * Retrieve image URL
     * @param $image
     * @return string
     */
    public function getImageUrl($image)
    {
		
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager= $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$image = strstr($image,'/media');
		$image = substr($image, strpos($image, "media/") + strlen('media/'));
		$url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).$image;
		return $url;
		
    }
	public function getThumbnailImagePath($_category)
{
    if ($_category->getThumbnail()) {
        $thumbnail_image = str_replace('/pub/', '', $_category->getThumbnail());
        $thumbnail_image = ltrim($thumbnail_image, '/'); // Remove leading slash if any

        if (str_contains($thumbnail_image, 'media/catalog/category/')) {
            return $thumbnail_image;
        } else {
            return 'media/catalog/category/' . $thumbnail_image;
        }
    }
    return null;
}

	public function getThumbnailImageUrl($_category)
    {	
		if($_category->getThumbnail()): 
		$thumbnail_image = $_category->getThumbnail();
		$isRelativeUrl = substr($thumbnail_image, 0, 1) === '/';
		if($isRelativeUrl):
			$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager= $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
			$storeId = $storeManager->getStore()->getStoreId();
			$thumbnail_image = strstr($thumbnail_image,'/media');
			$thumbnail_image = substr($thumbnail_image, strpos($thumbnail_image, "media/") + strlen('media/'));
			$_imgUrl = $storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).$thumbnail_image;
		else:
			$_imgUrl = $_category->getImageUrl('thumbnail');
		endif;
		return $_imgUrl;					
		endif;
		
		return null;
	}
	public function getCategoriesCollection($categoryId)
    {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$categoryFactory = $_objectManager->get('\Magento\Catalog\Model\CategoryFactory');
		
        $cat_collection = $categoryFactory->create()->load($categoryId);
		return $cat_collection;
    }
}