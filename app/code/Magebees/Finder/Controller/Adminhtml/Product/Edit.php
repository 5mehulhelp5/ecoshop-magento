<?php
namespace Magebees\Finder\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $_productsFactory;
    protected $_mapvalueFactory;
    protected $_ymmvalueFactory;
 
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_ymmvalueFactory = $ymmvalueFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
  
        parent::__construct($context);
    }
    
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Menu::title');
        return $resultPage;
    }
        
    public function execute()
    {
        
        $mapid     = (int) $this->getRequest()->getParam('id');
        $model  = $this->_mapvalueFactory->create()->load($mapid);
        //$valueid = $mapmodel->getYmmValueId();
        
        //$model = $this->_ymmvalueFactory->create()->load($valueid);
        //if ($valueid && !$model->getId()) {
        if ($mapid) {
            if (!$model->getId()) {
                $this->messageManager->addError(__('Product does not exist.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
   
        $this->_coreRegistry->register('finder_value', $model);
                    
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $mapid ? __('Edit Product') : __('Add Product'),
            $mapid ? __('Edit Product') : __('Add Product')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Magebees'));
        $resultPage->getConfig()->getTitle()->prepend(__('YMM Products Parts Finder'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Finders'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Product') : __('Add Product'));
        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
