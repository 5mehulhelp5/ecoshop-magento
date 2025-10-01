<?php
namespace Magebees\Productlisting\Controller\Adminhtml\Manage;

class MassStatus extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $listingIds = $this->getRequest()->getParam('listing');
        if (!is_array($listingIds) || empty($listingIds)) {
            $this->messageManager->addError(__('Please select product listing(s).'));
        } else {
            try {
                foreach ($listingIds as $listingId) {
                    $model = $this->_objectManager->get('Magebees\Productlisting\Model\Productlisting')->load($listingId);
                    $model->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully updated.', count($listingIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlisting::productlisting_content');
    }
}
