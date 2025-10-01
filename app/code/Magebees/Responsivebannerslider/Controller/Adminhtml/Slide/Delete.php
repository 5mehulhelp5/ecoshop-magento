<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slide;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class Delete extends \Magento\Backend\App\Action
{
    public function execute()
    {
		$bannerId = $this->getRequest()->getParam('id');
			
			try {
				$banner = $this->_objectManager->get('Magebees\Responsivebannerslider\Model\Slide')->load($bannerId);
				
				$filedir = $this->_objectManager->get('Magento\Framework\Filesystem');
				$mediaDir = $filedir->getDirectoryWrite(DirectoryList::MEDIA);
				$bannerDir = '/responsivebannerslider';
				$mediaDir->create($bannerDir);
				$mediaDir->changePermissions($bannerDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
				$bannerDir = $mediaDir->getAbsolutePath($bannerDir);
				$img_list_filename = $bannerDir.$banner['filename'];
				$dir = "thumbnails";
				$img_file = $bannerDir."/".$dir.$banner['filename'];
							
				if($banner['filename']) {
					if (file_exists($img_list_filename)) {
						unlink($img_list_filename);
					}
					if (file_exists($img_file)) {
						unlink($img_file);
					}
				} 
				$banner->delete();
				$mediaDirectory = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA); 

				$this->messageManager->addSuccess(
					__('Slides was successfully deleted')
				);
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
	}
	
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
