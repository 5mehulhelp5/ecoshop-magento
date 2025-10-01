<?php
namespace Magebees\Productlabel\Block;

class Productlabel extends \Magento\Framework\View\Element\Template
{
    /* Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
		array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
	  }
	
	public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
}
