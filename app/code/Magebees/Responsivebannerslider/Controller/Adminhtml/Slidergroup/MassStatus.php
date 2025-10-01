<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
class MassStatus extends \Magento\Backend\App\Action
{
   
    public function execute()
    {
		$bannerIds = $this->getRequest()->getParam('slidergroup');
		$status = $this->getRequest()->getParam('status');
		 
		if (!is_array($bannerIds) || empty($bannerIds)) {
            $this->messageManager->addError(__('Please select group(s).'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = $this->_objectManager->get('Magebees\Responsivebannerslider\Model\Responsivebannerslider')->load($bannerId);
					$banner->setData('status',$status)->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) were successfully updated.', count($bannerIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
		 $this->_redirect('*/*/');
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
