<?php

namespace Magebees\CacheWarmer\Plugin;

use Magebees\CacheWarmer\Model\FlushPagesManager;
use Magento\Framework\UrlInterface;

class OnePageCacheFlush
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var FlushPagesManager
     */
    private $flushPagesManager;

    public function __construct(
        UrlInterface $url,
        FlushPagesManager $flushPagesManager
    ) {
        $this->url = $url;
        $this->flushPagesManager = $flushPagesManager;
    }

    /**
     * Plugin to disable cache load if page need to be flushed
     *
     * @param Magento\Framework\App\PageCache\Kernel $subject
     * @param \Closure $proceed
     *
     * @return bool|mixed
     */
    public function aroundLoad($subject, \Closure $proceed)
    {
        $currentUrl = $this->url->getCurrentUrl();
        if ($page = $this->flushPagesManager->findPageToFlush($currentUrl)) {
            $this->flushPagesManager->deletePageToFlush($page);

            return false;
        }

        return $proceed();
    }
}
