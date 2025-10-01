<?php
namespace Magebees\Onepagecheckout\Model\System\Config\Source;
class Payment implements \Magento\Framework\Option\ArrayInterface
{
    protected $_modelTypeOnepage;
    protected $_paymentHelperData;
	
    public function __construct(
        \Magento\Checkout\Model\Type\Onepage $onePage,
        \Magento\Payment\Helper\Data $paymentHelperData
    )
    {
        $this->_modelTypeOnepage = $onePage;
        $this->_paymentHelperData = $paymentHelperData;
    }
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('-- Please select --'),
                'value' => '',
            ],
        ];
        $quote = $this->_modelTypeOnepage->getQuote();
        $store = $quote ? $quote->getStoreId() : null;
        $methods = $this->_paymentHelperData->getStoreMethods($store, $quote);
        foreach ($methods as $key => $method) {
            $options[] = [
                'label' => $method->getTitle(),
                'value' => $method->getCode(),
            ];
        }
        return $options;
    }
}