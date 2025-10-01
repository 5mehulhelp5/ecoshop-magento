<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\System\Config\TrustSeals\Renderer;
use Magento\Backend\Block\Template;

class Badge extends Template
{
    protected $_template = 'system/config/trust_seals/badge.phtml';

    public function setInputName($value)
    {
        return $this->setName($value);
    }
    public function setInputId($id)
    {
        return $this->setId($id);
    }
}
