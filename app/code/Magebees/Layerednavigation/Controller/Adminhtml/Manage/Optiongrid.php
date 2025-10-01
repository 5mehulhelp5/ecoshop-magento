<?php

namespace  Magebees\Layerednavigation\Controller\Adminhtml\Manage;

class Optiongrid extends \Magento\Backend\App\Action
{
    protected $resultLayoutFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
        getBlock('layernav.attribute.edit.tab.optioninfo.grid');
           
        return $resultLayout;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
