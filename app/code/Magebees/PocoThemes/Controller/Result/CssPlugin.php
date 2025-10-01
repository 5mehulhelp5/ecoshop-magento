<?php
namespace Magebees\PocoThemes\Controller\Result;

use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Plugin for processing relocation of javascript
 */
class CssPlugin
{
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
	
	
	
	protected $_driverfile;

    /**
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Filesystem\Driver\File $driverfile
	) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->_driverfile = $driverfile;
		
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
		
		
		//$css_merge_enabled = $this->scopeConfig->getValue('google_optimizer/setting/css_setting/merge_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); 
		$css_merge_enabled = true;
		if (PHP_SAPI === 'cli' || $this->request->isXmlHttpRequest() || !($css_merge_enabled)) {
            return $result;
        }
		
        $html = $response->getBody();
		 $content = $response->getBody();
		
		//$content = (string)$httpResponse->getContent();
        $headCloseTag = '</head>';
		
		//$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
        $headEndTagFound = strpos($content, $headCloseTag) !== false;

        if ($headEndTagFound) {
            $styles = $this->extractLinkTags($content);
            if ($styles) {
                $newHeadEndTagPosition = strrpos($content, $headCloseTag);
               // $content = substr_replace($content, $styles . "\n", $newHeadEndTagPosition, 0);
               // $httpResponse->setContent($content);
            }
        }
		
		/* $cssarray = array();
        
		
		
		if($css_merge_enabled){
		$csscontentcombine = "";
		if (preg_match_all('#<[^>]+text/css+[^>]+[^>]+all+[^>]+href\s{0,2}=\s{0,2}["\']{0,1}([^"\']+)["\']{0,1}[^>]+>#', $html, $match)) {
			$matchlength  = count($match[0]);
			for($i=0;$i <$matchlength;$i++ ){
						if($this->checkisFileAvailable($match[1][$i])==200)
						{ 
							$csscontent =  $this->_driverfile->fileGetContents($match[1][$i],false);
							$html = str_replace($match[0][$i],"<style>".$csscontent."</style>",$html);
						}
			}
		}
		}
		*/
		$response->setBody($content);
        return $result;
}
	public function checkisFileAvailable($file_url){
		$headers = @get_headers($file_url);
   		 return substr($headers[0], 9, 3);
		
	}
	private function extractLinkTags(string &$content): string
    { 
		$styles = '';
        $styleOpen = '<link  rel="stylesheet" type="text/css"  media="all"';
		//$styleOpen = '<link ';
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
           // $content = str_replace($style, '', $content);

            if (!preg_match('@href=("|\')(.*?)\1@', $style, $hrefAttribute)) {
                throw new \RuntimeException("Invalid link {$style} syntax provided");
            }
            $href = $hrefAttribute[2];

            if (preg_match('@media=("|\')(.*?)\1@', $style, $mediaAttribute)) {
                $media = $mediaAttribute[2];
            }
            $media = $media ?? 'all';

            /* $style = sprintf(
                '<link rel="stylesheet" media="%s" onload="this.onload=null;this.media=\'%s\'" href="%s">',
                $media,
				$media,
                $href
            );*/
			$stylesheet='stylesheet';
			$style_preload = sprintf(
                '<link rel="preload" media="%s" as="style" onload="this.onload=null;this.rel=\'%s\'" href="%s">',
                $media,
				$stylesheet,
                $href
            );
			 $content = str_replace($style, $style_preload, $content);
			 
           // $styles .= "\n" . $style;
		    $styles .= "\n" . $style_preload;
            // Link was cut out, search for the next one at its former position.
            $styleOpenPos = strpos($content, $styleOpen, $styleOpenPos);
        }

        return $styles;
    }
}

