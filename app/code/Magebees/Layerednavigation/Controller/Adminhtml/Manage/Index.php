<?php

namespace Magebees\Layerednavigation\Controller\Adminhtml\Manage;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Layerednavigation::grid');
        $this->_addBreadcrumb(__('Manage Attribute Filters'), __('Manage Attribute Filters'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Attribute Filters'));
        return $resultPage;
    }
    
    
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
