<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Plugin;

use Magebees\CacheWarmer\Model\PageStatus;

class ActionInterface
{
    /**
     * @var PageStatus
     */
    private $pageStatus;

    public function __construct(
        PageStatus $pageStatus
    ) {
        $this->pageStatus = $pageStatus;
    }

    public function beforeExecute(
        \Magento\Framework\App\ActionInterface $subject
    ) {
        $this->pageStatus->setStatus(PageStatus::STATUS_MISS);
    }
}
