<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
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
        $resultPage->setActiveMenu('Magebees_Responsivebannerslider::Heading')
            ->addBreadcrumb(__('Responsive banner slider'), __('Responsive banner slider'))
            ->addBreadcrumb(__('Manage Group'), __('Manage Group'));
        return $resultPage;
    }
    
    public function execute()
    {
         
        $id = $this->getRequest()->getParam('id');
		$model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Responsivebannerslider');
 
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
   
        $this->_coreRegistry->register('slidergroup_data', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Post') : __('Add Group'),
            $id ? __('Edit Post') : __('Add Group')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Slide'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('Add Group'));
 
        return $resultPage;
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
	
}
