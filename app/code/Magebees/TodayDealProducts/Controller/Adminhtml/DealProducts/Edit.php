<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
 
    public function __construct(
        Action\Context $context,
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
        $resultPage->setActiveMenu('Magebees_TodayDealProducts::todaydealpro_content');
        return $resultPage;
    }
        
    public function execute()
    {
        // 1. Get ID and create model
               
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Deal');
            
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            
            if (!$model->getId()) {
                $this->messageManager->addError(__('This record no longer exists.'));
          
                $resultRedirect = $this->resultRedirectFactory->create();
 
                return $resultRedirect->setPath('*/*/');
            }
        }
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
   
        $this->_coreRegistry->register('todaydeal_data', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Deal') : __('Add Deal'),
            $id ? __('Edit Deal') : __('Add Deal')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Magebees'));
        $resultPage->getConfig()->getTitle()->prepend(__('Today\'s Deal Products'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Deal Products'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('Add Deal'));
 
        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todaydealpro_content');
    }
}
