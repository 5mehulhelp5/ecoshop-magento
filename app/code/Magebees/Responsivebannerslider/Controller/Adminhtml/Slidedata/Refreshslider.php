<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidedata;
class Refreshslider extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $helper;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magebees\Responsivebannerslider\Helper\Data $helper
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->helper = $helper;
	}
    public function execute()
    {
        
        $dir_for_dynamic_file = 'pub/media/responsivebannerslider/files';
        $dir = $this->helper->getStaticTemplateDirectoryPath($dir_for_dynamic_file);
        $message = "Slide Refresh Successfully completed!";
        $refresh = false;
        try {
            $files = glob($dir . "*"); // get all file names
            if (!empty($files)) {
                foreach ($files as $file) { // iterate files
                    if (is_file($file)) {
                        $result = unlink($file); // delete file
                        if ($result) {
                            $refresh = true;
                        }
                    }
                }
            } else {
                $refresh = true;
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, null, 'responsivebannerslider.log');
        }
        if (!$refresh) {
            $message = "No Slider Found!";
        }
        $this->getResponse()->setBody($message);
    }
    protected function _isAllowed()
    {
        return true;
    }
}

