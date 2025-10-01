<?php

namespace Magebees\Testimonial\Controller\Adminhtml\Manage;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Testimonial::testimonial');
    }
}
