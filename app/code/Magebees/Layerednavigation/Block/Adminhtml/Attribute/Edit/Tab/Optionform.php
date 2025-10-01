<?php

namespace  Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab;

class Optionform extends \Magento\Backend\Block\Template
{
    protected $_systemStore;
     
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
       
        $this->_request=$context->getRequest();
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->_objectManager=$objectManager;
        parent::__construct($context, $data);
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
    public function getOptionData()
    {
        $storeId=(int)$this->getRequest()->getParam('store', 0);
         $optionId=(int)$this->getRequest()->getParam('id');
         $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('store_id', $storeId)
                                    ->addFieldToFilter('option_id', $optionId);
        return $opt_collection;
    }
    public function getBackUrl()
    {
        $session =$this->_objectManager->get('Magento\Backend\Model\Session');
        $attr_id=$session->getAttributeId();
        $url=$this->_urlBuilder->getUrl('layerednavigation/manage/edit', [ 'id' => $attr_id]);
        $url=$url.'?active_tab=option_section';
        return $url;
    }
    public function getOptionImageMediaDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'layerOption/images';
        return $path;
    }
    public function getOptionHoverMediaDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'layerOptionHover/images';
        return $path;
    }
}
