<?php

namespace Magebees\RichSnippets\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    public function __construct(
        Context $context     
       
    ) {
        parent::__construct($context);
       
    }
   public function getConfig($field)    
    {     
         return $this->scopeConfig->getValue($field,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }   
}
