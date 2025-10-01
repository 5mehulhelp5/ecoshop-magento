<?php
namespace Magebees\Pagebanner\Controller\Adminhtml\Manage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class Massstatus extends \Magento\Backend\App\Action
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
		$bannerIds = $this->getRequest()->getParam('banners');
        if (!is_array($bannerIds) || empty($bannerIds)) {
            $this->messageManager->addError(__('Please select banners.'));
        } else {
            try {
                $count=0;
                 $count=count($bannerIds);
                foreach ($bannerIds as $bannerId) {
					$banner = $this->Pagebanner->load($bannerId);
					$banner->setstatus(0);
					$banner->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been updated.', count($bannerIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Pagebanner::pagebanner_content');
    }
}
