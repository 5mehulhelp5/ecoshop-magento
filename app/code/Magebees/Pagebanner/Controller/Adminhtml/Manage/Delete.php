<?php
namespace Magebees\Pagebanner\Controller\Adminhtml\Manage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Delete extends \Magento\Backend\App\Action
{
	protected $Pagebanner;
	protected $filesystem;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magebees\Pagebanner\Model\Pagebanner $Pagebanner,
		\Magento\Framework\Filesystem $filesystem
    ) {
    
        parent::__construct($context);
    	$this->Pagebanner = $Pagebanner;
		$this->filesystem = $filesystem;
		
    }
    public function execute()
    {
        
		$id = $this->getRequest()->getParam('id');
        try {
				$data = array();
                $banner = $this->Pagebanner->load($id);
				
				
				$mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
				$bannerDir = '/pagebanner';
				$bannerDir = $mediaDir->getAbsolutePath($bannerDir);
				
				
				if($banner['banner_image']) {
					$img_list_filename = $bannerDir.$banner['banner_image'];
					$img_file = $bannerDir."/".$banner['banner_image'];
					if (file_exists($img_list_filename)) {
						unlink($img_list_filename);
					}
					if (file_exists($img_file)) {
						unlink($img_file);
					}
				}
				
				$banner->delete();
				$this->messageManager->addSuccess(
                    __('Page Banner was deleted successfully!')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Pagebanner::pagebanner_content');
    }
}
