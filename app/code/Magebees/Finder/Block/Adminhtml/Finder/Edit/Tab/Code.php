<?php
namespace Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab;

class Code extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'code.phtml';
    
    /**
     * Prepare form
     *
     * @return $this
     */
    
    public function getFinderData()
    {
        $model = $this->_coreRegistry->registry('finder_data');
        return $model->getFinderId();
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
