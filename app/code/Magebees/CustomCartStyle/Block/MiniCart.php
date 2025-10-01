<?php
/**
 * Copyright © 2016 Magebees. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\CustomCartStyle\Block;

class MiniCart extends \Magento\Framework\View\Element\Template
{
    
    /* @var \Magebees\CustomCartStyle\Helper\Data */
    protected $helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\CustomCartStyle\Helper\Data $helper,
        array $data = []
    ){
        $this->helper = $helper;
        parent::__construct($context, $data);
    }
        
    public function getHelper()
    {
        return $this->helper;
    }
}