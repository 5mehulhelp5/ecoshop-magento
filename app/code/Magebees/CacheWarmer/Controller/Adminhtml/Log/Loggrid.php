<?php
namespace  Magebees\CacheWarmer\Controller\Adminhtml\Log;

class Loggrid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\CacheWarmer\Block\Adminhtml\Warmerlog\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_CacheWarmer::log');
    }
}
