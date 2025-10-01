<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

class Delete extends \Magento\Backend\App\Action
{
    protected $_dealFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\TodayDealProducts\Model\DealFactory $dealFactory
    ) {
        parent::__construct($context);
        $this->_dealFactory = $dealFactory;
    }

    public function execute()
    {
        $todaydealId = $this->getRequest()->getParam('id');
        try {
            $todaydeal = $this->_dealFactory->create()->load($todaydealId);
            $todaydeal->delete();
            $this->messageManager->addSuccess(
                __('Deal Deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
