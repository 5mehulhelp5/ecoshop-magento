<?php
namespace Magebees\Blog\Controller\Adminhtml\Comment;
class NewAction extends \Magento\Backend\App\Action
{
   
    public function execute()
    {
        $this->_forward('edit');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
