<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
		if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magebees_Responsivebannerslider::grid');
        $this->_addBreadcrumb(__('Responsive Banner Slider'), __('Responsive Banner Slider'));
        $this->_addBreadcrumb(__('Manage Group'), __('Manage Group'));
        $this->_view->renderLayout();
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}