<?php

namespace Magebees\Advertisementblock\Block\Adminhtml;

class Dynamicform extends \Magento\Backend\Block\Template
{
    protected $request;
	protected $helper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magebees\Advertisementblock\Helper\Data $helper,
        array $data = []
    ) {
       
        $this->request = $context->getRequest();
          $this->helper = $helper;
        parent::__construct($context, $data);
    }
    public function getPatternValue()
    {
        $param=$this->request->getParams();
        $pattern=$param['pattern'];
        return $pattern;
    }
    public function getNumberOfField($pattern)
    {
        $field_arr=$this->helper->getNumberOfBlock();
        return $field_arr[$pattern];
    }
}
