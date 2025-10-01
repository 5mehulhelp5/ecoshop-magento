<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

class MassStatus extends \Magento\Backend\App\Action
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
                    //print_R($model->getData());exit;
                    $model->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully updated.', count($finderIds))
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
