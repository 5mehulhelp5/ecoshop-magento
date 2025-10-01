<?php
 namespace Magebees\Testimonial\Block;

 use Magento\Store\Model\ScopeInterface;

class TestimonialLink extends \Magento\Framework\View\Element\Html\Link
{

    protected $_scopeConfig;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
       
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('testimonial/index/index', ['_secure' => true]);
    }
    
    public function getLabel()
    {
        
        return __($this->_scopeConfig->getValue('testimonial/setting/toplink_title', ScopeInterface::SCOPE_STORE));
    }
    
    public function isEnable()
    {
        
        return $this->_scopeConfig->getValue('testimonial/setting/enable_toplink', ScopeInterface::SCOPE_STORE);
    }
}
