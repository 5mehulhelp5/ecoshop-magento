<?php
namespace Magebees\Imagegallery\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Imagegallery\Model\Imagegallery;
use Magento\Framework\App\Filesystem\DirectoryList;
class Gallery extends Template {
    protected $imagegallery;
	protected $_storeManager;
	protected $helper;
	protected $imageFactory;
	protected $imageAdapterFactory;
	protected $_filesystem;
	protected $_directory;
    public function __construct(
		Context $context,
		\Magebees\Imagegallery\Helper\Data $helper,
		\Magebees\Imagegallery\Model\Imagegallery $Imagegallery,
		\Magento\Store\Model\StoreManagerInterface $storemanager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\Factory $imageFactory,
		\Magento\Framework\Image\AdapterFactory $imageAdapterFactory)
	{
        
        $this->imagegallery = $Imagegallery;
        $this->_storemanager = $storemanager;
        $this->helper = $helper;
        $this->imageFactory = $imageFactory;
		$this->imageAdapterFactory = $imageAdapterFactory;
        $this->_filesystem = $filesystem;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }
    
    public function getImageGalleryCollection() {
		
       $storeId = $this->_storemanager->getStore()->getId();
        
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->postCollection->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        
		$collection->setOrder('creation_time', 'ASC');
	   return $collection;
    }
    
   
    
    public function getFeaturedImage($post,$width=null,$height=null,$resize_type=null) {
        $featured_image = $post->getFeaturedImg();
        if ($post->getFeaturedImg()):
            $image_size = $this->configuration->getFeatureImageSize();
			if((!$width) && (!$height))
			{
			$width = $image_size['width'];
			$height = $image_size['height'];	
			}
			if(!$resize_type)
			{
				$resize_type = $image_size['resize_type'];
			}
		 	return $this->getFeaturedImageResize($featured_image, $width, $height,$resize_type);
        else:
            return null;
        endif;
    }
	
	
	
    public function EnablePublishDate() {
        return $post_publication_date = $this->configuration->getConfig('blog/date/post_publication_date');
    }
    
  public function checkReseizeImageAvailable($imageName, $width, $height ,$type='resizeonly'){
		
		$image_name_array = (explode("/",(string)$imageName));
		$imageName = end($image_name_array);
		if($imageName)
		{
			$targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/resized/' . $width . 'x' . $height.'/'.$imageName);
			if ($this->_directory->isFile($this->_directory->getRelativePath($targetDir))) {
				return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $imageName;
			}
		}else{
			return false;
		}
		return false;
	}
	public function getFeaturedImageResize($imageName, $width = 258, $height = 200,$type='resizeonly') {
		$resizeImagePath =  $this->checkReseizeImageAvailable($imageName, $width, $height ,$type);
		if($resizeImagePath){
			return $resizeImagePath;
		}
		$realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . $imageName);
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            return false;
        }
		
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/resized/' . $width . 'x' . $height);
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);
      
        if (!$this->_directory->isExist($pathTargetDir)) {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir)) {
            return false;
        }
		
		if($type=='cropandresize')
		{
			$image = $this->imageAdapterFactory->create();
		$image->open($realPath);
		$currentRatio = $image->getOriginalWidth() / $image->getOriginalHeight();
        $targetRatio = $width / $height;
		$image->keepAspectRatio(false);
		if ($targetRatio > $currentRatio) {
            $image->resize($width, null);
        } else {
            $image->resize(null, $height);
        }
		//$image->resize($width, $height);
		$diffWidth  = $image->getOriginalWidth() - $width;
        $diffHeight = $image->getOriginalHeight() - $height;

		
		 $image->crop(
            floor($diffHeight * 0.5),
            floor($diffWidth / 2),
            ceil($diffWidth / 2),
            ceil($diffHeight * 0.5)
        );
		
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
			if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}else {
			
		$image = $this->imageAdapterFactory->create();
        $image->open($realPath);
        $image->keepAspectRatio(true);
        $image->resize($width, $height);
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
		if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}
	    return false;
	}
    
}
