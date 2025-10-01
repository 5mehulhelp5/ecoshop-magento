<?php
namespace Magebees\Finder\Controller\Adminhtml\Product;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
