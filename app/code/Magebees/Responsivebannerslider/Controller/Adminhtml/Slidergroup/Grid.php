<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
class Grid extends \Magento\Backend\App\Action
{
	public function execute()
	{
		$this->getResponse()->setBody($this->_view->getLayout()->createBlock('Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Grid')->toHtml());
	}
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
