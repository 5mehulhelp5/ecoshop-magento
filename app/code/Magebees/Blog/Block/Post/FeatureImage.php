<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\App\Filesystem\DirectoryList;
class FeatureImage extends \Magebees\Blog\Block\Post\View
{
	 
   	
	public function getPostFeatureImage()
	{
		if(($this->post_id)&& ($this->_currentPost)):
		$featured_image = $this->_currentPost->getFeaturedImg();
		if($featured_image):
			$realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . $featured_image);
			if ($this->_directory->isFile($realPath) || $this->_directory->isExist($realPath)) {
				return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog'. $featured_image;
			}
		endif;
		endif;
		return null;
		
		
	}
	
}

