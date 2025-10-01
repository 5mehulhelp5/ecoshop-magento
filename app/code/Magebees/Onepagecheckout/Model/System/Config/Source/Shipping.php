<?php
namespace Magebees\Onepagecheckout\Model\System\Config\Source;
class Shipping implements \Magento\Framework\Option\ArrayInterface
{
    protected $_scopeConfig;
    protected $_carrierFactory;
	
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_carrierFactory = $carrierFactory;
    }
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('-- Please select --'),
                'value' => '',
            ],
        ];
        foreach ($this->_scopeConfig->getValue('carriers') as $code => $carrier) {
			if(isset($carrier['active'])){
				$active = $carrier['active'];
				if ($active == 1 || $active == true) {
					if (isset($carrier['title'])) {
						$options[] = [
							'label' => $carrier['title'],
							'value' => $code,
						];
					}
				}
			}
		}
        return $options;		
    }
}