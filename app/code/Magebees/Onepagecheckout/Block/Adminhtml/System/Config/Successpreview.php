<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\System\Config;
class Successpreview extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $urlModel; 
	
    public function __construct(\Magento\Framework\Url $urlModel,\Magento\Backend\Block\Template\Context $context,array $data = [])
    {
        $this->urlModel = $urlModel;
        parent::__construct($context, $data);
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $link = $this->getFrontendLink();
        $html = '<div class="successPreview"><a href="' . $link . '" target="_new">'. __('Preview Success Page'). '</a></div>';
        return $html;
    }
    public function getFrontendLink()
    {
        $storeId = $this->_getStoreId();
        $url = $this->urlModel->setScope($storeId)->getUrl('onepage/index/successpreview');
        return $url;
    } 
    protected function _getStoreId()
    {
        $storeId = $this->_request->getParam('store');
        if (!empty($storeId)) {
            return (string) $storeId;
        }
        return (string)$this->_storeManager->getStore()->getCode();
    } 
}