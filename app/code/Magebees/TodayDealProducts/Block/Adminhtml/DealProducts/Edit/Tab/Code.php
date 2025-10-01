<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab;

class Code extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'code.phtml';
    
    /**
     * Prepare form
     *
     * @return $this
     */
    
    public function getTodayDealData()
    {
        $model = $this->_coreRegistry->registry('todaydeal_data');
        return $model->getTodayDealId();
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
