<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Controller\Adminhtml\Queue;

use Magebees\CacheWarmer\Model\Queue;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Unlock extends \Magebees\CacheWarmer\Controller\Adminhtml\Queue
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(
        Context $context,
        Queue $queue
    ) {
        parent::__construct($context);
        $this->queue = $queue;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->queue->forceUnlock();
        $this->messageManager->addSuccessMessage(__('Unlocked successfully!'));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
