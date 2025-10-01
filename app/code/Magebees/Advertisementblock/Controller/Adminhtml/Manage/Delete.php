<?php
namespace Magebees\Advertisementblock\Controller\Adminhtml\Manage;

class Delete extends \Magento\Backend\App\Action
{
  
    public function execute()
    {
        $AdvertisementblockId=(int) $this->getRequest()->getParam('id');
        if ($AdvertisementblockId) {
            try {
                $Advertisementblock = $this->_objectManager->get('Magebees\Advertisementblock\Model\Advertisementinfo')->load($AdvertisementblockId);
                    $Advertisementblock->delete();
                
                 $this->messageManager->addSuccess(
                     __('Your Advertisement Block Record have been deleted.')
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
