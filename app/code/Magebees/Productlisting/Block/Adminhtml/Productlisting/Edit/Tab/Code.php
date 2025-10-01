<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab;

class Code extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'code.phtml';
    
    /**
     * Prepare form
     *
     * @return $this
     */
    
    public function getProductlistingData()
    {
        $model = $this->_coreRegistry->registry('productlisting_data');
        return $model->getListingId();
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
