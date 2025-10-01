<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Clear extends Action
{
    /**
     * @var \Magebees\CacheWarmer\Model\ResourceModel\Log
     */
    private $logResource;

    public function __construct(
        Context $context,
        \Magebees\CacheWarmer\Model\ResourceModel\Log $logResource
    ) {
        parent::__construct($context);
        $this->logResource = $logResource;
    }

    public function execute()
    {
        try {
            $this->logResource->flush();

            $this->messageManager->addSuccessMessage(__('Warmer log has been successfully cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_CacheWarmer::log_clear');
    }
}
