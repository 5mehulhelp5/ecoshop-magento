<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
use Magento\Framework\App\Filesystem\DirectoryList;
class Gallery extends \Magebees\Blog\Block\Post\View {
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if ($this->getRequest()->getParam('post_id')) {
            $this->post_id = $this->getRequest()->getParam('post_id');
            $this->_currentPost = $this->_blogpost->load($this->post_id);
        }
        return $this;
    }
    public function getPostGallery() {
        $post = $this->getPost();
        $_mediaUrl = $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $media_gallery = explode(",", (string)$post->getMediaGallery());
		$post_gallery = array();
	
        foreach ($media_gallery as $image):
			
			$resized_path = $this->getResizeImage($image);
			if($resized_path):
			$post_gallery[] = array('full_path' => $_mediaUrl . 'magebees_blog/gallery' . $image, 'path' => $image , 'resized_path' => $resized_path);
			endif;
        endforeach;
		return $post_gallery;
    }
    public function getResizeImage($imageName, $width = 258, $height = 200) {
        /* Real path of image from directory */
        $realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/gallery' . $imageName);
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            return false;
        }
        /* Target directory path where our resized image will be save */
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/gallery/resized/' . $width . 'x' . $height);
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);
        /* If Directory not available, create it */
        if (!$this->_directory->isExist($pathTargetDir)) {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir)) {
            return false;
        }
        $image = $this->imageFactory->create();
        $image->open($realPath);
        $image->keepAspectRatio(true);
        $image->resize($width, $height);
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
        if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/gallery/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
        return false;
    }
}
