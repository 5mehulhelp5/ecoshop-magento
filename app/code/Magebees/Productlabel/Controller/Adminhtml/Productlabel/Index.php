<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magebees_Productlabel::label';

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
        $resultPage->setActiveMenu('Magebees_Productlabel::label');
        $resultPage->addBreadcrumb(__('Product Labels'), __('Product Labels'));
        $resultPage->addBreadcrumb(__('Manage Labels'), __('Manage Labels'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Labels'));

        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlabel::label');
    }
}
