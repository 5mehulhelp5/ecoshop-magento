<?php

declare(strict_types = 1);

namespace Magebees\CacheWarmer\Plugin\Framework\Authorization;

use Magebees\CacheWarmer\Helper\Http as HttpHelper;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Authorization;

class AllowManageCustomer
{
    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        HttpHelper $httpHelper
    ) {
        $this->httpHelper = $httpHelper;
    }

    public function afterIsAllowed(
        Authorization $subject,
        bool $result,
        string $resource
    ): bool {
        if ($resource !== AccountManagement::ADMIN_RESOURCE
            || !$this->httpHelper->isCrawlerRequest()
        ) {
            return $result;
        }

        return true;
    }
}
