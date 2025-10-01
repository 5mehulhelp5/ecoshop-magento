<?php
namespace Magebees\Finder\Controller\Adminhtml\Product;

class Tab extends \Magento\Backend\App\Action
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magebees\Finder\Model\Mapvalue');
        $model->load($id);
        $this->_coreRegistry->register('finder_value', $model);
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
        getBlock('finder_edit_tab_products_grid')
            ->setFinderProduct($this->getRequest()->getPost('finder_product', null));
        return $resultLayout;
    }
        
    protected function _isAllowed()
    {
        return true;
    }
}
