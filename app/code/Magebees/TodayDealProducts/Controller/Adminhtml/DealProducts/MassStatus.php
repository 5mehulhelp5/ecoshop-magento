<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;
use Magento\Framework\Controller\ResultFactory;

class MassStatus extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $todaydealIds = $this->getRequest()->getParam('todaydeal');
		
        if (!is_array($todaydealIds) || empty($todaydealIds)) {
            $this->messageManager->addError(__('Please select deal(s).'));
        } else {
            try {
                foreach ($todaydealIds as $todaydealId) {
                    $model = $this->_objectManager->get('Magebees\TodayDealProducts\Model\Deal')->load($todaydealId);
					$model->setIsActive($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully updated.', count($todaydealIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
		
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
	}
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todaydealpro_content');
    }
}
