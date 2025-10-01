<?php
namespace  Magebees\Advertisementblock\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\Advertisementblock\Block\Adminhtml\Advertisementblock\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Advertisementblock::advertisementblock');
    }
}
