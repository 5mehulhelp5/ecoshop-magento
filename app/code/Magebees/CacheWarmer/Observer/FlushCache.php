<?php
namespace Magebees\CacheWarmer\Observer;

use Magebees\CacheWarmer\Model\Queue;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class FlushCache implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Queue\RegenerateHandler
     */
    private $regenerateHandler;

    public function __construct(
        LoggerInterface $logger,
        Queue\RegenerateHandler $regenerateHandler
    ) {
        $this->logger = $logger;
        $this->regenerateHandler = $regenerateHandler;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $this->regenerateHandler->execute();
        } catch (\Exception $e) {
            $this->logger->critical(__('Unable to regenerate queue: %1', $e->getMessage()));
        }
    }
}
