<?php

namespace Magebees\CacheWarmer\Controller\Adminhtml\Queue;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Backend\App\Action\Context;

class Process extends \Magebees\CacheWarmer\Controller\Adminhtml\Queue
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        Queue $queue,
        Config $config
    ) {
        parent::__construct($context);
        $this->queue = $queue;
        $this->config = $config;
    }

    public function execute()
    {
		if ($this->getRequest()->isAjax()) {
			
			$this->queue->setBatchSizeAjax();
			
			$crawledPages = $this->queue->process();
			
			if ($crawledPages) {
				//$this->messageManager->addSuccess(__('Cache warmer queue Batch process finished'));
			} else {           
			
				//$this->messageManager->addError(__('Something went wrong while process the warmer queue'));
			}
        }
		
		$this->getResponse()->representJson($crawledPages);

    }
}
