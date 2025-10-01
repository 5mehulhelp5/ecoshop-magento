<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Controller\Adminhtml\Queue;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Backend\App\Action\Context;

class Generate extends \Magebees\CacheWarmer\Controller\Adminhtml\Queue
{
    /**
     * @var Queue\RegenerateHandler
     */
    private $regenerateHandler;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        Queue\RegenerateHandler $regenerateHandler,
        Config $config
    ) {
        parent::__construct($context);
        $this->regenerateHandler = $regenerateHandler;
        $this->config = $config;
    }

    public function execute()
    {
        try {
            if ($this->config->isModuleEnabled()) {
                list($result, $processedItems) = $this->regenerateHandler->execute(true);

                if ($result) {
                    $this->messageManager->addSuccessMessage(
                        __('Warmer queue has been successfully generated for %1 URLs.', $processedItems)
                    );
                } else {
                    $this->messageManager->addWarningMessage(__('Warmer queue was disturbed by another process'));
                }
            } else {
                $this->messageManager->addWarningMessage(
                    __('The warming queue cannot be generated and warmed up because the module is disabled.')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
