<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

class MassDelete extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $finderIds = $this->getRequest()->getParam('finder');
        
        if (!is_array($finderIds) || empty($finderIds)) {
            $this->messageManager->addError(__('Please select finder(s).'));
        } else {
            try {
                foreach ($finderIds as $finderId) {
                    $model = $this->_objectManager->get('Magebees\Finder\Model\Finder')->load($finderId);
                    $model->delete();
                }
                        
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been deleted.', count($finderIds))
                    );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
