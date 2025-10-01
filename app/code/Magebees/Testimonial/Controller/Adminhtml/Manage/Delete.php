<?php
namespace Magebees\Testimonial\Controller\Adminhtml\Manage;

class Delete extends \Magento\Backend\App\Action
{
  
    public function execute()
    {
        $testimonialId=(int) $this->getRequest()->getParam('id');
        if ($testimonialId) {
            try {
                $testimonial = $this->_objectManager->get('Magebees\Testimonial\Model\Testimonialcollection')->load($testimonialId);
                    $testimonial->delete();
                
                 $this->messageManager->addSuccess(
                     __('Your Testimonial Record have been deleted.')
                 );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Testimonial::testimonial');
    }
}
