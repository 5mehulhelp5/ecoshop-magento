<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Debug;

use Magento\Framework\Model\AbstractModel;

class DebugContext extends AbstractModel
{
    public const URL = 'url';
    public const CONTEXT_DATA = 'context_data';

    protected function _construct()
    {
        $this->_init(\Magebees\CacheWarmer\Model\ResourceModel\DebugContext::class);
    }

    public function setUrl(string $url)
    {
        $this->setData(self::URL, $url);

        return $this;
    }

    public function getUrl(): string
    {
        return (string)$this->_getData(self::URL);
    }

    public function setContextData(string $contextJson)
    {
        $this->setData(self::CONTEXT_DATA, $contextJson);

        return $this;
    }

    public function getContextDataJson(): string
    {
        return (string)$this->_getData(self::CONTEXT_DATA);
    }
}
