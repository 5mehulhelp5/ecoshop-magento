<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Controller\Reports;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\ReportsFactory;
use Magebees\CacheWarmer\Model\ResourceModel\Reports;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManager;

class Index extends Action
{
    public const HIT_STATUS = 'hit';
    public const MISS_STATUS = 'miss';

    /**
     * @var ReportsFactory
     */
    private $reportsFactory;

    /**
     * @var Reports
     */
    private $reportsResource;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        ReportsFactory $reportsFactory,
        Reports $reports,
        SessionManager $sessionManager,
        Config $config
    ) {
        parent::__construct($context);
        $this->reportsFactory = $reportsFactory;
        $this->reportsResource = $reports;
        $this->request = $context->getRequest();
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }

    public function execute()
    {
        if ($this->config->isVarnishEnabled()) {
            if ($this->sessionManager->getIsVarnishHit() === null) {
                $status = self::HIT_STATUS;
            } else {
                $status = self::MISS_STATUS;
                $this->sessionManager->setIsVarnishHit(null);
            }
        } else {
            $status = $this->sessionManager->getPageStatus();
        }

        if ($status !== self::HIT_STATUS) {
            $status = self::MISS_STATUS;
        }

        $resposneTime = (float)$this->request->getParam('ttfb');

        $report = $this->reportsFactory->create();
        $report->setData(['status' => $status, 'response' => $resposneTime]);
        $this->reportsResource->save($report);
    }
}
