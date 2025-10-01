<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

class MassStatus extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $labelIds = $this->getRequest()->getParam('label');
        if (!is_array($labelIds) || empty($labelIds)) {
            $this->messageManager->addError(__('Please select lable(s).'));
        } else {
            try {
                foreach ($labelIds as $labelId) {
                    $model = $this->_objectManager->get('Magebees\Productlabel\Model\Productlabel')->load($labelId);
                    $model->setIsActive($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully updated.', count($labelIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlabel::label');
    }
}
