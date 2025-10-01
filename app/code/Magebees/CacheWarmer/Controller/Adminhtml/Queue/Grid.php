<?php
namespace Magebees\CacheWarmer\Controller\Adminhtml\Queue;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\CacheWarmer\Block\Adminhtml\Cachewarmer\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_CacheWarmer::queue');
    }
}
