<?php
namespace Magebees\Responsivebannerslider\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class ResponsivebannersliderObserver implements ObserverInterface
{
	protected $_slide;
	protected $scopeConfig;
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magebees\Responsivebannerslider\Model\Slide $slide,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

		) {

		$this->_filesystem = $filesystem;
		$this->scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$this->_imageFactory = $imageFactory;
		$this->_slide = $slide;

	}
	 
    public function execute(\Magento\Framework\Event\Observer $observer){
				
		$dir = "thumbnails";
		$width = $this->scopeConfig->getValue('responsivebannerslider/setting/thumbnail_width',ScopeInterface::SCOPE_STORE);;
		if(trim($width) == "" || trim($width) < 0){
			$width = "200";
		}

		$collection = $this->_slide->getCollection();
		
		foreach($collection as $slidedata) {
		
			$fileName = $slidedata['filename'];
						
			if($fileName != '') {
				$mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
				$bannerDir = '/responsivebannerslider';
				$mediaDir->create($bannerDir);
				$mediaDir->changePermissions($bannerDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
				$bannerDir = $mediaDir->getAbsolutePath($bannerDir);
				$absPath = $bannerDir.$fileName;
				$imageResized = $bannerDir."/".$dir.$fileName;
				
				if ($width != '') {
					
					if (file_exists($imageResized)) {
						unlink($imageResized);
					} 
					
					$imageResize = $this->_imageFactory->create();
					$imageResize->open($absPath);
					$imageResize->constrainOnly(TRUE);
					$imageResize->keepTransparency(TRUE);
					$imageResize->keepFrame(FALSE);
					$imageResize->keepAspectRatio(true);
					$imageResize->resize($width);
					$dest = $imageResized ;
					$imageResize->save($dest);
				}
				$path = $bannerDir."/".$dir;
				
				if( chmod($path, 0777) ) {
					chmod($path, 0755);
				}

				$paths = $bannerDir;
				if( chmod($paths, 0777) ) {
					chmod($paths, 0755);
				}
			}	
		}
	
		return true;
	}
}