<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Plugin;

use Magebees\CacheWarmer\Helper\Http as HttpHelper;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;

class SessionManager
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magebees\CacheWarmer\Model\SessionManagerFactory
     */
    private $sessionManagerFactory;

    /**
     * @var HttpHelper
     */
    private $httpHelper;

    /**
     * @var bool
     */
    private $wasStarted = false;

    public function __construct(
        RequestInterface $request,
        \Magebees\CacheWarmer\Model\SessionManagerFactory $sessionManagerFactory,
        HttpHelper $httpHelper
    ) {
        $this->request = $request;
        $this->sessionManagerFactory = $sessionManagerFactory;
        $this->httpHelper = $httpHelper;
    }

    public function afterStart(
        \Magento\Customer\Model\Session $subject
    ) {
        if (!$this->httpHelper->isCrawlerRequest()) {
            return;
        }

        if ($this->wasStarted) {
            return;
        }
        $this->wasStarted = true;

        $customerGroup = (int)$this->request->getHeader(HttpHelper::CUSTOMER_GROUP_HEADER, Group::NOT_LOGGED_IN_ID);
        $currency = $this->request->getHeader(HttpHelper::CURRENCY_HEADER);

        if (!preg_match('#[A-Z]{3}#', $currency)) {
            $currency = false;
        }

        // IMPORTANT
        //
        // We should pass this instance of customer session into constructor
        // because we are still in \Magento\Framework\Session\SessionManager::__construct and attempt of getting
        // \Magento\Customer\Model\Session singleton will cause a circular dependency error

        /** @var \Magebees\CacheWarmer\Model\SessionManager $crawlerSessionManager */
        $crawlerSessionManager = $this->sessionManagerFactory->create([
            'customerSession' => $subject
        ]);

        $crawlerSessionManager
            ->setCustomerGroup($customerGroup)
            ->setCurrency($currency);
    }
	
	public function beforeGetCustomerGroupId(Session $subject)
    {
        if ($this->httpHelper->isCrawlerRequest()) {
            $subject->setCustomerGroupId(null);
        }
    }
	
}
