<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\Order\View\Comments;
use Magento\Framework\View\Element\Template;

class Comments extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
