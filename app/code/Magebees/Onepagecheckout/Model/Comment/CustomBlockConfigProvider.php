<?php
namespace Magebees\Onepagecheckout\Model\Comment;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class CustomBlockConfigProvider implements ConfigProviderInterface
{
    protected $scopeConfiguration;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
    ) {
        $this->scopeConfiguration = $scopeConfiguration;
    }
    public function getConfig()
    {
        $showHide = [];
        $enabled = $this->scopeConfiguration->getValue('magebees_Onepagecheckout/general/onepage_checkout_comments_enabled', ScopeInterface::SCOPE_STORE);
        $showHide['show_hide_custom_block'] = ($enabled) ? true:false;
        return $showHide;
    }
}