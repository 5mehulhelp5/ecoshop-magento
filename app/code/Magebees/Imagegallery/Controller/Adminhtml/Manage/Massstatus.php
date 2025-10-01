<?php
namespace Magebees\Imagegallery\Controller\Adminhtml\Manage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class Massstatus extends \Magento\Backend\App\Action
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
	
		$imageIds = $this->getRequest()->getParam('images');
		$status = $this->getRequest()->getParam('status');
		
        if (!is_array($imageIds) || empty($imageIds)) {
            $this->messageManager->addError(__('Please select images.'));
        } else {
            try {
                $count=0;
                 $count=count($imageIds);
                foreach ($imageIds as $imageId) {
					$imagegallery = $this->Imagegallery->load($imageId);
					$imagegallery->setstatus($status);
					$imagegallery->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been updated.', count($imageIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Imagegallery::imagegallery_content');
    }
}
