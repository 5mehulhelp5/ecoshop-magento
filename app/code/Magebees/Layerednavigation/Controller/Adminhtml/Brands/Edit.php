<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Brands;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
     
    protected $_coreRegistry = null;
    protected $resultPageFactory;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
 
   
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Layerednavigation::brand')
            ->addBreadcrumb(__('Magebees_Layernavigation'), __('Magebees_Layernavigation'))
            ->addBreadcrumb(__('Manage Brands'), __('Manage Brands'));
        return $resultPage;
    }
 
   
    public function execute()
    {
   
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magebees\Layerednavigation\Model\Brands');
 
        if ($id) {
            $model->load($id);
                    
            if (!$model->getId()) {
                $this->messageManager->addError(__('This grid record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
 
  
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
   
        $this->_coreRegistry->register('brands_data', $model);
         
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Post') : __('Add Brand'),
            $id ? __('Edit Post') : __('Add Brand')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getBrandName() : __('Add Brand'));
 
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::brand');
    }
}
