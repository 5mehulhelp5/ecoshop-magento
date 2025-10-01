<?php
namespace Magebees\Finder\Controller\Adminhtml\Import;

class MassDelete extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $history_ids = $this->getRequest()->getParam('history');
        
        if (!is_array($history_ids) || empty($history_ids)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($history_ids as $history_id) {
                    $model = $this->_objectManager->get('Magebees\Finder\Model\History')->load($history_id);
					$finder_id = $model->getFinderId();
                    $model->delete();
                }
                        
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been deleted.', count($history_ids))
                    );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('finder/finder/edit', ['id' => $finder_id]);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
