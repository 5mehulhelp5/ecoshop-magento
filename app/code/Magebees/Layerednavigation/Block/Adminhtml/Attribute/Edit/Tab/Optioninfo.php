<?php

namespace  Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab;

class Optioninfo extends \Magento\Backend\Block\Template
{
    protected $_systemStore;
    protected $_template = 'store_switcher.phtml';
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\System\Store $storedata,
        array $data = []
    ) {
        $this->storedata = $storedata;
        parent::__construct($context, $data);
    }
    public function getCurrenturl()
    {
        $attr_id = $this->getRequest()->getParam('id');
        $url=$this->_urlBuilder->getUrl('layerednavigation/manage/edit', [ 'id' => $attr_id]);
        return $url;
    }
    public function getCurrentStore()
    {
        $store=(int)$this->getRequest()->getParam('store', 0);
        return $store;
    }

    public function getStoreData()
    {
        return $this->storedata->getStoreValuesForForm(false, true);
    }
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab\Optioninfo\Grid',
                'options.grid'
            )
        );
        parent::_prepareLayout();
        return $this;
    }
     
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
