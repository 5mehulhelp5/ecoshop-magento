<?php
namespace Magebees\Onepagecheckout\Plugin\Braintree;
class ChannelDataBuilder
{
    public function afterBuild(
        \Magento\Braintree\Gateway\Request\ChannelDataBuilder $buildSubject,
        $result
    )
    {
        if (isset($result['channel'])) {
            $result['channel'] = 'Magento-Magebees';
        }
        return $result;
    }
}