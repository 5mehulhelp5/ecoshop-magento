<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slide;
class Grid extends \Magento\Backend\App\Action
{
	public function execute()
	{
		$this->getResponse()->setBody($this->_view->getLayout()->createBlock('Magebees\Responsivebannerslider\Block\Adminhtml\Slide\Grid')->toHtml());
	}
	 protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
