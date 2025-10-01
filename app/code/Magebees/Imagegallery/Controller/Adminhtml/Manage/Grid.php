<?php
namespace Magebees\Imagegallery\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->getResponse()->setBody($this->_view->getLayout()->createBlock('Magebees\Imagegallery\Block\Adminhtml\Imagegallery\Grid')->toHtml());
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Imagegallery::imagegallery_content');
    }
}
