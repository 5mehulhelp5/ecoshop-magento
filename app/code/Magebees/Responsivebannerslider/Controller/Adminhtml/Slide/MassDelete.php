<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slide;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class MassDelete extends \Magento\Backend\App\Action
{
   
    public function execute()
    {
		$slideIds = $this->getRequest()->getParam('slide');
		
		if (!is_array($slideIds) || empty($slideIds)) {
            $this->messageManager->addError(__('Please select slide(s).'));
        } else {
            try {
                foreach ($slideIds as $bannerId) {
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
				}	
						
					$this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($slideIds))
					);
					
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
		 $this->_redirect('*/*/');
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
