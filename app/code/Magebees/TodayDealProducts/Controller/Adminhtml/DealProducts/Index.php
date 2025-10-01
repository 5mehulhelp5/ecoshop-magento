<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_TodayDealProducts::todaydealpro_content');
        $resultPage->addBreadcrumb(__('Today\'s Deal Products'), __('Today\'s Deal Products'));
        $resultPage->addBreadcrumb(__('Manage Today\'s Deal Products'), __('Manage Today\'s Deal Products'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Today\'s Deal Products'));

        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todaydealpro_content');
    }
}
