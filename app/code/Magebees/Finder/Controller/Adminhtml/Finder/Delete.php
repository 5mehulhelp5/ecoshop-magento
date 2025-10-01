<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

class Delete extends \Magento\Backend\App\Action
{
    protected $_finderFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Finder\Model\FinderFactory $finderFactory
    ) {
        parent::__construct($context);
        $this->_finderFactory = $finderFactory;
    }

    public function execute()
    {
        $finderId = $this->getRequest()->getParam('id');
        try {
            $finder = $this->_finderFactory->create()->load($finderId);
            $finder->delete();
            $this->messageManager->addSuccess(
                __('Finder Deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
