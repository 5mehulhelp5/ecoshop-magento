<?php
namespace Magebees\PocoThemes\Model\Controller;

use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Plugin for processing relocation of javascript
 */
class ResultPlugin
{
    const EXCLUDE_FLAG_PATTERN = 'data-rocketjavascript="false"';

    /**
     * Request
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var bool
     */
    protected $allowedOnPage;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface|null $storeManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ?\Magento\Store\Model\StoreManagerInterface $storeManager = null
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeManager = $storeManager ?: $objectManager->get(
            \Magento\Store\Model\StoreManagerInterface::class
        );
    }

    /**
     * @param \Magento\Framework\Controller\ResultInterface $subject
     * @param callable $proceed
     * @param ResponseHttp $response
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRenderResult(
        \Magento\Framework\Controller\ResultInterface $subject,
        \Closure $proceed,
        ResponseHttp $response
    ) {
        $result = $proceed($response);
        if (PHP_SAPI === 'cli' || $this->request->isXmlHttpRequest() || !$this->isEnabled()) {
            return $result;
        }
		
        if (($this->request->getFullActionName() == 'catalog_product_view')||($this->request->getFullActionName() == 'checkout_cart_configure')) {
			return $result;
		}
        

        $ignoredStrings = null;
        $ignoredStrings = explode("\n", str_replace("\r", "\n", (string)$ignoredStrings));
        foreach ($ignoredStrings as $key => $ignoredString) {
            $ignoredString = trim($ignoredString);
            if (!$ignoredString) {
                unset($ignoredStrings[$key]);
            } else {
                $ignoredStrings[$key] = $ignoredString;
            }
        }

        $html = $response->getBody();
        $scripts = [];

        $startTag = '<script';
        $endTag = '</script>';

        $start = 0;
        $i = 0;
        while (false !== ($start = stripos($html, $startTag, $start))) {
            $i++;
            if ($i > 1000) {
                return $result;
            }

            $end = stripos($html, $endTag, $start);
            if (false === $end) {
                break;
            }

            $len = $end + strlen($endTag) - $start;
            $script = substr($html, $start, $len);

            if (false !== stripos($script, self::EXCLUDE_FLAG_PATTERN)) {
                $start++;
                continue;
            }

            foreach ($ignoredStrings as $ignoredString) {
                if (false !== stripos($script, $ignoredString)) {
                    $start++;
                    continue 2;
                }
            }

            $html = str_replace($script, '', $html);
            $scripts[] = $script;
        }

        $scripts = implode(PHP_EOL, $scripts);
        $end = stripos($html, '</body>');
        if ($end !== false) {
            $html = substr($html, 0, $end) . $scripts . substr($html, $end);
        } else {
            $html .= $scripts;
        }

        $response->setBody($html);

        return $result;
    }

    private function isEnabled()
    {
		return true;
	}
}
