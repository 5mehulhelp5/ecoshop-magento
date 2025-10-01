<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class MassDelete extends \Magento\Backend\App\Action
{
   
    public function execute()
    {
        $labelIds = $this->getRequest()->getParam('label');
        
        if (!is_array($labelIds) || empty($labelIds)) {
            $this->messageManager->addError(__('Please select label(s).'));
        } else {
            try {
                foreach ($labelIds as $labelId) {
                    $model = $this->_objectManager->get('Magebees\Productlabel\Model\Productlabel')->load($labelId);
                    $model->delete();
                }
                        
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been deleted.', count($labelIds))
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
