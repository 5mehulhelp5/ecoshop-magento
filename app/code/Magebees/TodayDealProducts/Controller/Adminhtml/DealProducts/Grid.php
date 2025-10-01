<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->getResponse()->setBody($this->_view->getLayout()->createBlock('Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Grid')->toHtml());
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todaydealpro_content');
    }
}
