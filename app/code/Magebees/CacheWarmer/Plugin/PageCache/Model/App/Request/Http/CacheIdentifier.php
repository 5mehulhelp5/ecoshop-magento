<?php

declare(strict_types=1);

namespace Magebees\CacheWarmer\Plugin\PageCache\Model\App\Request\Http;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\DesignExceptions;
use Magento\PageCache\Model\App\Request\Http\IdentifierForSave;
use Magento\PageCache\Model\Config;
use Magento\Store\Model\StoreManager;

// Fix magento 2.4.7 local bug https://github.com/magento/magento2/issues/38626
class CacheIdentifier
{
    /**
     * @var DesignExceptions
     */
    private $designExceptions;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        DesignExceptions $designExceptions,
        RequestInterface $request,
        Config $config,
        ProductMetadataInterface $productMetadata
    ) {
        $this->designExceptions = $designExceptions;
        $this->request = $request;
        $this->config = $config;
        $this->productMetadata = $productMetadata;
    }

    public function afterGetValue(IdentifierForSave $identifier, string $result): string
    {
        if ($this->productMetadata->getVersion() !== '2.4.7') {
            return $result;
        }

        if ($this->config->getType() === Config::BUILT_IN && $this->config->isEnabled()) {
            $identifierPrefix = '';
            /* @phpstan-ignore-next-line */
            $ruleDesignException = $this->designExceptions->getThemeByRequest($this->request);
            if ($ruleDesignException !== false) {
                $identifierPrefix .= 'DESIGN' . '=' . $ruleDesignException . '|';
            }

            /* @phpstan-ignore-next-line */
            if ($runType = $this->request->getServerValue(StoreManager::PARAM_RUN_TYPE)) {
                $identifierPrefix .= StoreManager::PARAM_RUN_TYPE . '=' .  $runType . '|';
            }

            /* @phpstan-ignore-next-line */
            if ($runCode = $this->request->getServerValue(StoreManager::PARAM_RUN_CODE)) {
                $identifierPrefix .= StoreManager::PARAM_RUN_CODE . '=' . $runCode . '|';
            }

            return $identifierPrefix . $result;
        }

        return $result;
    }
}
