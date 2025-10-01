<?php

namespace Magebees\Navigationmenu\Controller\Adminhtml\Menudata;

class RefreshMenu extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $helper;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magebees\Navigationmenu\Helper\Data $helper
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->helper = $helper;
	}
    public function execute()
    {
        $dir_for_dynamic_file = 'pub/media/magebees/navigationmenu';
        $dir = $this->helper->getStaticTemplateDirectoryPath($dir_for_dynamic_file);
        $message = "Menu Refresh Successfully completed!";
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
            $this->messageManager->addException($e, null, 'navigationmenu.log');
        }
        if (!$refresh) {
            $message = "Menu is not refresh successfully!";
        }
        $this->getResponse()->setBody($message);
    }
    protected function _isAllowed()
    {
        return true;
    }
}

