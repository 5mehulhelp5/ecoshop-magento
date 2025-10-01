<?php

namespace Magebees\CacheWarmer\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Information extends Template
{
    const ENQUEUE_COMMAND = 'magebees:warmqueue:generate';
    const DEQUEUE_COMMAND = 'magebees:warmcache';

    public function getText()
    {
        return __(
            '<pre>CLI commands that can be run to execute:' .
            '<br/>Queue Generation:    \'bin/magento ' . self::ENQUEUE_COMMAND . '\'' .
            '<br/>Pages Warming Up:    \'bin/magento ' . self::DEQUEUE_COMMAND . '\'</pre>'
        );
    }
}
