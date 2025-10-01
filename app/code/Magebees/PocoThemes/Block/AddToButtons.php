<?php
namespace Magebees\PocoThemes\Block;
class AddToButtons extends \Magento\Catalog\Block\Product\AbstractProduct
{	
	protected $urlHelper;
	protected $_scopeConfig;
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        
        parent::__construct(
            $context,
            $data
        );
        
        $this->urlHelper = $urlHelper;
    }

    public function _toHtml()
    {
        $hover_effects = $this->_scopeConfig->getValue('pocothemes/pro_list/hover_effects', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $template = 'Magento_Catalog::product/'.$hover_effects.'.phtml';
		$this->setTemplate($template);
        return parent::_toHtml();
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
            'product' => $product->getEntityId(),
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

}