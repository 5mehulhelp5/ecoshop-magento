<?php
namespace Magebees\CacheWarmer\Observer;

use Magebees\CacheWarmer\Block\Status as StatusBlock;
use Magebees\CacheWarmer\Helper\Http as HttpHelper;
use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\PageStatus;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context as ContextHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

class SendResponseBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var PageStatus
     */
    private $pageStatus;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * @var HttpHelper
     */
    private $httpHelper;

    /**
     * @var Queue\ProcessMetaInfo
     */
    private $processMetaInfo;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        Config $config,
        BlockFactory $blockFactory,
        PageStatus $pageStatus,
        StoreManagerInterface $storeManager,
        SessionManager $sessionManager,
        Queue $queue,
        ContextHelper $contextHelper,
        HttpHelper $httpHelper,
        Queue\ProcessMetaInfo $processMetaInfo,
        ?Session $customerSession = null
    ) {
        $this->config = $config;
        $this->blockFactory = $blockFactory;
        $this->pageStatus = $pageStatus;
        $this->storeManager = $storeManager;
        $this->sessionManager = $sessionManager;
        $this->logger = $contextHelper->getLogger();
        $this->queue = $queue;
        $this->contextHelper = $contextHelper;
        $this->httpHelper = $httpHelper;
        $this->processMetaInfo = $processMetaInfo;
        $this->customerSession = $customerSession ?? ObjectManager::getInstance()->get(Session::class);
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getData('request');

        if ($request->isAjax() || !$request->isGet() || $this->httpHelper->isCrawlerRequest()) {
            return;
        }

        /** @var ResponseInterface $response */
        $response = $observer->getData('response');

        if (!$response instanceof \Magento\Framework\App\Response\Http) {
            return;
        }

       
        $status = $this->pageStatus->getStatus();
        $this->sessionManager->setPageStatus($status);
        if ($this->config->isVarnishEnabled()) {
            $this->sessionManager->setIsVarnishHit(false);
        }

        if (!$this->config->canDisplayStatus()) {
            return;
        }

        if ($status == PageStatus::STATUS_IGNORED) { // Block already rendered
            return;
        }

        $body = $response->getBody();

        /** @var StatusBlock $block */
        $block = $this->blockFactory->createBlock(\Magebees\CacheWarmer\Block\Status::class);
        $block->setData('status', $status);
        $html = $block->toHtml();

        $body = str_replace(StatusBlock::BLOCK_PLACEHOLDER, $html, $body);

        $response->setBody($body);
    }

    private function isMobile()
    {
        $httpUserAgent = $this->contextHelper->getHttpHeader()->getHttpUserAgent();
        if (isset($httpUserAgent) && $this->config->isProcessMobile()) {
            $regexp = $this->config->getUserAgents();

            if (preg_match('@' . $regexp . '@', $httpUserAgent)) {
                return true;
            }
        }

        return false;
    }
}
