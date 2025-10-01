<?php
namespace Magebees\PocoBase\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Fbfanbox extends Template  implements BlockInterface
{
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        //$this->setTemplate('widget/fbfanbox.phtml');
    }
    

}