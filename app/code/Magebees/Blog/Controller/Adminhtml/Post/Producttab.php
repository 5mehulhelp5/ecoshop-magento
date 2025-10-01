<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;

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
        getBlock('post_edit_tab_selectproduct')
            ->setSelectProduct($this->getRequest()->getPost('select_product', null));
        return $resultLayout;
    }
        
    protected function _isAllowed()
    {
        return true;
    }
}
