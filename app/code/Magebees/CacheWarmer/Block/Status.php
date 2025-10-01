<?php
namespace Magebees\CacheWarmer\Block;

use Magebees\CacheWarmer\Model\Config as ConfigProvider;
use Magebees\CacheWarmer\Model\PageStatus;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout\Element;
use Magento\PageCache\Model\Config;

class Status extends Template
{
    public const BLOCK_PLACEHOLDER = '<!-- magebees-cachewarmer-status -->';

    /**
     * @var string
     */
    protected $_template = 'Magebees_CacheWarmer::status.phtml';

    /**
     * @var ConfigProvider
     */
    private $config;
    /**
     * @var PageStatus
     */
    private $pageStatus;

    public function __construct(
        Template\Context $context,
        ConfigProvider $config,
        PageStatus $pageStatus,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->pageStatus = $pageStatus;
    }

    public function getNotCacheableBlocks()
    {
        if (!$this->hasData('not_cacheable_blocks')) {
            $result = [];

            $nodes = $this->_layout->getXpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]');

            if ($nodes) {
                /** @var Element $node */
                foreach ($nodes as $node) {
                    $result [] = [
                        'name' => $node->getAttribute('name'),
                        'class' => $node->getAttribute('class'),
                    ];
                }
            }

            if ($result) {
                $this->pageStatus->setStatus(PageStatus::STATUS_IGNORED);
            }

            $this->setData('not_cacheable_blocks', $result);
        }

        return $this->getData('not_cacheable_blocks');
    }

    public function getStatusCode()
    {
        if ($this->getData('status')) {
            return $this->getData('status');
        }

        if ($this->getNotCacheableBlocks()) {
            return PageStatus::STATUS_IGNORED;
        }

        return PageStatus::STATUS_UNDEFINED;
    }

    public function getStatusTitle()
    {
        $titles = [
            PageStatus::STATUS_UNDEFINED => __('Undefined'),
            PageStatus::STATUS_HIT       => __('Cache Hit'),
            PageStatus::STATUS_MISS      => __('Cache Miss'),
            PageStatus::STATUS_IGNORED   => __('Ignored'),
        ];

        $statusCode = $this->getStatusCode();

        if (isset($titles[$statusCode])) {
            return $titles[$statusCode];
        } else {
            return $titles[PageStatus::STATUS_UNDEFINED];
        }
    }

    public function isContextDataDebug(): bool
    {
        return $this->config->isDebugContext();
    }

    public function getDebugContextUrl(): string
    {
        return $this->getUrl('magebees_cachewarmer/contextdata/index');
    }

    protected function _toHtml()
    {
        /**
         * In case of using Varnish or other caching application we must render block as it is for debugging purposes
         */
        if ($this->config->canDisplayStatus()
            && $this->config->isDebugContext()
            && $this->_scopeConfig->getValue(Config::XML_PAGECACHE_TYPE) != Config::BUILT_IN
        ) {
            return parent::_toHtml();
        }

        if ($this->isStatusUndefined() || !$this->config->canDisplayStatus()) {
            return self::BLOCK_PLACEHOLDER;
        } else {
            return parent::_toHtml();
        }
    }

    protected function isStatusUndefined()
    {
        return !$this->hasData('status') && !$this->getNotCacheableBlocks();
    }
}
