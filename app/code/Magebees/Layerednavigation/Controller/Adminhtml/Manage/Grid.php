<?php
namespace  Magebees\Layerednavigation\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\Layerednavigation\Block\Adminhtml\Attribute\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
