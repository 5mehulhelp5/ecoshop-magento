<?php
namespace Magebees\Imagegallery\Controller\Adminhtml\Manage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Delete extends \Magento\Backend\App\Action
{
	protected $Imagegallery;
	protected $filesystem;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magebees\Imagegallery\Model\Imagegallery $Imagegallery,
		\Magento\Framework\Filesystem $filesystem
    ) {
    
        parent::__construct($context);
    	$this->Imagegallery = $Imagegallery;
		$this->filesystem = $filesystem;
		
    }
    public function execute()
    {
        
		$imageId = $this->getRequest()->getParam('id');
        try {
				$data = array();
				$Imagegallery = $this->Imagegallery->load($imageId);
				if($Imagegallery->getImageId()):
				$image = $Imagegallery->getData();
				$mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
				$bannerDir = '/imagegallery';
				$bannerDir = $mediaDir->getAbsolutePath($bannerDir);
				if($image['image']) {
					$img_list_filename = $bannerDir.$image['image'];
					$img_file = $bannerDir."/".$image['image'];
					if (file_exists($img_list_filename)) {
						unlink($img_list_filename);
					}
					if (file_exists($img_file)) {
						unlink($img_file);
					}
					}
				$Imagegallery->delete();
				$this->messageManager->addSuccess(
                    __('Image Gallery was deleted successfully!')
                );
				endif;
				
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Imagegallery::imagegallery_content');
    }
}
