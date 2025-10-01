<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slide;
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magebees_Responsivebannerslider::slide');
        $this->_addBreadcrumb(__('Responsive Banner Slider'), __('Responsive Banner Slider'));
        $this->_addBreadcrumb(__('Manage Slide'), __('Manage Slide'));
        $this->_view->renderLayout();
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}