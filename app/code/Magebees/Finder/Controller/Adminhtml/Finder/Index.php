<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

class Index extends \Magento\Backend\App\Action
{
    //const ADMIN_RESOURCE = 'Magebees_Finder::finder';

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
        $resultPage->setActiveMenu('Magebees_Finder::finder');
        $resultPage->addBreadcrumb(__('YMM Products Parts Finder'), __('YMM Products Parts Finder'));
        $resultPage->addBreadcrumb(__('Manage Finders'), __('Manage Finders'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Finders'));

        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
