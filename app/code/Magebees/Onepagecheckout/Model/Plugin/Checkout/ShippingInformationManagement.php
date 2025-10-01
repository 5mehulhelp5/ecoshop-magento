<?php
namespace Magebees\Onepagecheckout\Model\Plugin\Checkout;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\ShippingInformationManagement as ShippingManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magebees\Onepagecheckout\Helper\Configurations as Helper;

class ShippingInformationManagement
{    
    protected $_helper;
    protected $_quoteRepository;

    public function __construct(
        QuoteRepository $quoteRepository,
        Helper $helper
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_helper = $helper;
    }

    public function beforeSaveAddressInformation(
        ShippingManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if ($this->_helper->getEnable()) {
            $extAttributes = $addressInformation->getExtensionAttributes();
            $newsletterSubscribe = $extAttributes->getNewsletterSubscribe() ? 1 : 0;
            $quote = $this->_quoteRepository->getActive($cartId);
            $quote->setNewsletterSubscribe($newsletterSubscribe);
        }
    }
}