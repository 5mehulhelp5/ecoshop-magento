<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

class Delete extends \Magento\Backend\App\Action
{
    protected $_productlabelFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Productlabel\Model\ProductlabelFactory $productlabelFactory
    ) {
        parent::__construct($context);
        $this->_productlabelFactory = $productlabelFactory;
    }

    public function execute()
    {
        $labelId = $this->getRequest()->getParam('id');
        try {
            $label = $this->_productlabelFactory->create()->load($labelId);
            $label->delete();
            $this->messageManager->addSuccess(
                __('Label Deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
