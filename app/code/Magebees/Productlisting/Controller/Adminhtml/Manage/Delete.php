<?php
namespace Magebees\Productlisting\Controller\Adminhtml\Manage;

class Delete extends \Magento\Backend\App\Action
{
    protected $_productlistingFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Productlisting\Model\ProductlistingFactory $productlistingFactory
    ) {
        parent::__construct($context);
        $this->_productlistingFactory = $productlistingFactory;
    }

    public function execute()
    {
        $listingId = $this->getRequest()->getParam('id');
        try {
            $listing = $this->_productlistingFactory->create()->load($listingId);
            $listing->delete();
            $this->messageManager->addSuccess(
                __('Listing Deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
