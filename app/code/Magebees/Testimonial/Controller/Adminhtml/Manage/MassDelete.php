<?php

namespace  Magebees\Testimonial\Controller\Adminhtml\Manage;

class MassDelete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if (!is_array($testimonialIds) || empty($testimonialIds)) {
            $this->messageManager->addError(__('Please select testimonial(s).'));
        } else {
            try {
                $count=0;
                 $count=count($testimonialIds);
                foreach ($testimonialIds as $testimonialId) {
                    $testimonial = $this->_objectManager->get('Magebees\Testimonial\Model\Testimonialcollection')->load($testimonialId);
                    $testimonial->delete();
                }
                 $this->messageManager->addSuccess(
                     __('A total of   '.$count .'  record(s) have been deleted.', count($testimonialIds))
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
