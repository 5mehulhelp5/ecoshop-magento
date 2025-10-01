<?php
namespace Magebees\Pagebanner\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->getResponse()->setBody($this->_view->getLayout()->createBlock('Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Grid')->toHtml());
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Pagebanner::pagebanner_content');
    }
}
