<?php

namespace Magebees\Layerednavigation\Controller\Adminhtml\Manage;

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
   
    public function execute()
    {
        // 1. Get ID and create model
        
        $id = $this->getRequest()->getParam('id');
        
                 $session = $this->_objectManager->get('Magento\Backend\Model\Session');

         $session->setAttributeId($id);
         
         
        $model = $this->_objectManager->create('Magebees\Layerednavigation\Model\Layerattribute');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        
        
        // 2. Initial checking
        if ($id) {
            $model->load($id, 'attribute_id');
            if (!$model->getId()) {
                $this->messageManager->addError(__('Attribute Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('layer_attribute', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Layerednavigation::grid');
        $this->_addBreadcrumb(__('Attribute Information'), __('Attribute Information'));
        $resultPage->getConfig()->getTitle()->prepend(__('Attribute Information'));
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
