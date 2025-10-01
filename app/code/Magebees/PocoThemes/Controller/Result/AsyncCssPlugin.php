<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magebees\PocoThemes\Controller\Result;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;

/**
 * Plugin for asynchronous CSS loading.
 */
class AsyncCssPlugin
{
 
	
	private const XML_PATH_CSS_MERGE = 'dev/css/merge_css_files';
	private const XML_PATH_USE_CSS_CRITICAL_PATH = 'dev/css/use_css_critical_path';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Extracts styles to head after critical css if critical path feature is enabled.
     *
     * @param Layout $subject
     * @param Layout $result
     * @param HttpResponseInterface|ResponseInterface $httpResponse
     * @return Layout (That should be void, actually)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRenderResult(Layout $subject, Layout $result, ResponseInterface $httpResponse)
    {
		
       if ($this->isCssCriticalEnabled()) {
            return $result;
        }
	if (!$this->isCssCriticalEnabled()) {
        $content = (string)$httpResponse->getContent();
        $headCloseTag = '</head>';
		
		//$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
        $headEndTagFound = strpos($content, $headCloseTag) !== false;

        if ($headEndTagFound) {
            $styles = $this->extractLinkTags($content);
            if ($styles) {
                $newHeadEndTagPosition = strrpos($content, $headCloseTag);
				$content = substr_replace($content, $styles . "\n", $newHeadEndTagPosition, 0);
				//$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
                $httpResponse->setContent($content);
            }
        }

        return $result;     
    }
    
    }

    /**
     * Extracts link tags found in given content.
     *
     * @param string $content
     */
    private function extractLinkTags(string &$content): string
    {
        $styles = '';
        $styleOpen = '<link';
        $styleClose = '>';
        $styleOpenPos = strpos($content, $styleOpen);

        while ($styleOpenPos !== false) {
            $styleClosePos = strpos($content, $styleClose, $styleOpenPos);
            $style = substr($content, $styleOpenPos, $styleClosePos - $styleOpenPos + strlen($styleClose));

            if (!preg_match('@rel=["\']stylesheet["\']@', $style)) {
                // Link is not a stylesheet, search for another one after it.
                $styleOpenPos = strpos($content, $styleOpen, $styleClosePos);
                continue;
            }
            // Remove the link from HTML to add it before </head> tag later.
            $content = str_replace($style, '', $content);

            if (!preg_match('@href=("|\')(.*?)\1@', $style, $hrefAttribute)) {
                throw new \RuntimeException("Invalid link {$style} syntax provided");
            }
            $href = $hrefAttribute[2];

            if (preg_match('@media=("|\')(.*?)\1@', $style, $mediaAttribute)) {
                $media = $mediaAttribute[2];
            }
            $media = $media ?? 'all';
			
			
			/* $style = sprintf(
                '<link rel="stylesheet" type="text/css" media="%s" href="%s">',
                $media,
                $href
            ); */
      
			/* Code Added By Ajay Start */
			if($this->isMergeCssEnabled()):
			$pre_load_style = sprintf('<link rel="preload" as="style"  href="%s">',$href);
			$style = sprintf('<link rel="stylesheet" href="%s">',$href);
			$styles .= "\n" . $pre_load_style;
			$styles .= "\n" . $style;
			else:
			$style = sprintf(
                '<link rel="stylesheet" type="text/css" media="%s" href="%s">',
                $media,
                $href
            );
			$styles .= "\n" . $style;
			endif;
			/* Code Added By Ajay End */
			
			
            // Link was cut out, search for the next one at its former position.
            $styleOpenPos = strpos($content, $styleOpen, $styleOpenPos);
        }

        return $styles;
    }

    /**
     * Returns information whether css critical path is enabled
     *
     * @return bool
     */
    private function isCssCriticalEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_USE_CSS_CRITICAL_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }
	private function isMergeCssEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CSS_MERGE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
