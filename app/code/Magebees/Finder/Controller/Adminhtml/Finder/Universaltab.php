<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

class Universaltab extends \Magento\Backend\App\Action
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
        /*$id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magebees\Finder\Model\Finder');
		$model->load($id);
		$this->_coreRegistry->register('finder_data', $model);*/
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
        getBlock('finder_edit_tab_finder_universalproduct')
            ->setUniversalProducts($this->getRequest()->getPost('universal_products', null));
        return $resultLayout;
    }
        
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
