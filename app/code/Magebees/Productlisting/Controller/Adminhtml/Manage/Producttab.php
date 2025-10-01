<?php
namespace Magebees\Productlisting\Controller\Adminhtml\Manage;

class Producttab extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $resultLayoutFactory;
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_coreRegistry = $registry;
    }
    
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
        getBlock('productlisting_edit_tab_productlisting_selectproduct')
            ->setSelectProducts($this->getRequest()->getPost('select_products', null));
        return $resultLayout;
    }
        
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlisting::productlisting_content');
    }
}
