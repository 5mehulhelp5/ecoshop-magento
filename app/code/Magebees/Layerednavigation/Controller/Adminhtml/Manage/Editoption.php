<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Editoption extends \Magento\Backend\App\Action
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
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
   
    public function execute()
    {
        
         $store=(int)$this->getRequest()->getParam('store', 0);
         $optionid=(int)$this->getRequest()->getParam('id');
         $session = $this->_objectManager->get('Magento\Backend\Model\Session');
         $session->setTestKey($store);
         $session->setOptionId($optionid);
        $resultPage = $this->resultPageFactory->create();
         $resultPage->setActiveMenu('Magebees_Layerednavigation::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Attribute Option'));
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
