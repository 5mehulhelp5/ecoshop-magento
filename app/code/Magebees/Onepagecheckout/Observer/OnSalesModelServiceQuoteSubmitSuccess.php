<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Newsletter\Model\Subscriber;
use Magebees\Onepagecheckout\Helper\Configurations as Helper;
class OnSalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{
    protected $_subscriber;
    protected $_helper;
    protected $_logger;

    public function __construct(
        Subscriber $subscriber,
        Helper $helper,
        LoggerInterface $logger
    ) {
        $this->_subscriber = $subscriber;
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getEnable()) {
            $quote = $observer->getQuote();
            if ($quote->getNewsletterSubscribe()) {
                $email = 'undefined';
                try {
                    $email = $quote->getCustomerEmail();
                    $this->_subscriber->subscribe($email);
                } catch (\Exception $e) {
                    $this->_logger->error($e->getMessage() . 'to ' . $email);
                }
            }
        }
        return $this;
    }
}
