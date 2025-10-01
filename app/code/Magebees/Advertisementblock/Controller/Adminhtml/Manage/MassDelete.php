<?php

namespace  Magebees\Advertisementblock\Controller\Adminhtml\Manage;

class MassDelete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $AdvertisementblockIds = $this->getRequest()->getParam('advertisementblock');
        if (!is_array($AdvertisementblockIds) || empty($AdvertisementblockIds)) {
            $this->messageManager->addError(__('Please select Advertisement block(s).'));
        } else {
            try {
                foreach ($AdvertisementblockIds as $AdvertisementblockId) {
                    $Advertisementblock = $this->_objectManager->get('Magebees\Advertisementblock\Model\Advertisementinfo')->load($AdvertisementblockId);
                    $Advertisementblock->delete();
                }
                 $this->messageManager->addSuccess(
                     __('A total of %1 record(s) have been deleted.', count($AdvertisementblockIds))
                 );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Advertisementblock::advertisementblock');
    }
}
