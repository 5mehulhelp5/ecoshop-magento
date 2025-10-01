<?php

namespace Magebees\Testimonial\Controller\Adminhtml\Manage;

class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        // 1. Get ID and create model
        
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magebees\Testimonial\Model\Testimonialcollection');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('Testimonial Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('testimonial', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Testimonial::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('Testimonial Information'));
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Testimonial::testimonial');
    }
}
