<?php

namespace Magebees\Advertisementblock\Controller\Adminhtml\Manage;

class Index extends \Magento\Backend\App\Action
{	
	protected $resultPageFactory;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Advertisementblock::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Advertising Blocks'));
        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Advertisementblock::advertisementblock');
    }
}
