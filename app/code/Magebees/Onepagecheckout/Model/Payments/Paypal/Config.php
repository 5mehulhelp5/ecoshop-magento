<?php
namespace Magebees\Onepagecheckout\Model\Payments\Paypal;
use \Magento\Paypal\Model\Config as paypalConfig;

class Config extends paypalConfig
{
    public function getBuildNotationCode()
    {
        return 'Magebees_SI_MagentoCE_WPS';
    }
}