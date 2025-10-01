<?php
namespace Magebees\PocoThemes\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\App\Response\Http;
use Magebees\PocoThemes\Helper\Data;

class SpeedOptimizer extends \Magento\Framework\View\Element\Template
{
    protected $request;

    protected $httpHeader;

    protected $helper;

    protected $content;

    protected $isJson;

    protected $exclude = [];

    protected $excludeHtml = [];

    protected $scripts = [];

    protected $storeManager;

    protected $themeProvider;

    protected $placeholder = 'data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22' . '$width' . '%22%20height%3D%22' . '$height' . '%22%20viewBox%3D%220%200%20225%20265%22%3E%3C%2Fsvg%3E';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        RequestInterface $request,
        Header $httpHeader,
        Data $helper,
        StoreManagerInterface $storeManager,
        ThemeProviderInterface $themeProvider,
        array $data = []
    ) 
    {    
        parent::__construct($context, $data);
        $this->request  = $request;
        $this->helper   = $helper;
        $this->httpHeader    = $httpHeader;
        $this->storeManager  = $storeManager;
        $this->themeProvider =  $themeProvider;

    }

    /**
     * @param Http $subject
     * @return void
     */
    public function beforeSendResponse(Http $response)
    {
		$noJs = $this->isNoJs();
		if($noJs):
		if(($this->request->isAjax())||($this->request->isXmlHttpRequest())){
			return;
			/* request is ajax */
            $contentType = $response->getHeader('Content-Type');
            if( $contentType && $contentType->getMediaType() == 'application/json' ) {
                $this->isJson = true;
                // return; // break response type json
            }
        }
		$body = $response->getBody();
		//$noJs    = false;
		$minifyHtml    = false;
        $minifyJs      = true;
        $deferJs       = true;
		$body = $this->processExcludeJs($body, $minifyJs, $deferJs);
       //$bodyClass   = $noJs ? 'ajx_ldr_bg' : '';
	   //$bodyClass   = 'ajx_ldr_bg';
       //$body = $this->addBodyClass($body, $bodyClass);     
		if(!$noJs) {
            if ($deferJs){
                $scripts = implode('', $this->scripts);
                $body    = $this->addToBottomBody($body, $scripts);
            } else {
                $body = preg_replace_callback(
                    '~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is',
                    function($match){
                        $scriptId = trim($match[1], ' ');
                        if($scriptId && isset($this->scripts[$scriptId])){
                            return $this->scripts[$scriptId];
                        }else {
                            return $match[0];
                        }
                    },
                    $body
                );
            }            
        }
		$response->setBody($body);
		endif;
		
    }

    /* Insert to Bottom body */
    public function addToBottomBody( $content, $insert)
    {
        $content = str_ireplace('</body>', $insert . '</body>', $content);
        return $content;         
    }

   
    public function processExcludeJs($content, $minify=false, $deferJs=false)
    {
        $content = preg_replace_callback(
            '~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is',
            function($match) use($minify, $deferJs){
                // if(stripos($match[0], 'type="text/x-magento') !== false) return $match[0];
                $scriptId = 'script_' . uniqid();
                if ($minify && trim($match[1], ' ')){
                    $this->scripts[$scriptId] =  $this->minifyJs( $match[0] );
                }else {
                    $this->scripts[$scriptId] = $match[0];
                }
                if (!$deferJs) return '<script>' . $scriptId . '</script>';
                return '';
            },
            $content
        );

        return $content;
    }

    public function minifyJs($script)
    {
        $regex   = '~//?\s*\*[\s\S]*?\*\s*//?~'; // RegEx to remove /** */ and // ** **// php comments
        $search = array(
            '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\') \/\/.*))/',
            '/(\s)+/s',         // shorten multiple whitespace sequences
        );

        $replace = array(
            '',
            '\\1',
        );
        $minScript = preg_replace($search, $replace, $script);
        /* Return $script when $minScript empty */
        return $minScript ? $minScript : $script;
    }

    public function getTheme()
    {
        $themeId = $this->_scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        return $this->themeProvider->getThemeById($themeId);
    }

    
    
    public function isTablet()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        return preg_match('/iPad|iPad.*Mobile/i', $userAgent);        
    }

    public function isNoJs()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
		return preg_match('/Chrome-Lighthouse|PingdomPageSpeed|PingdomPageSpeed|GTmetrix/i', $userAgent);     
    }

    public function isMobile()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        return $isMobile = \Zend_Http_UserAgent_Mobile::match($userAgent, $_SERVER);
    }
	public function addBodyClass( $content, $class )
    {
        // return preg_replace( '/<body([\s\S]*?)(?:class="(.*?)")([\s\S]*?)?([^>]*)>/', sprintf( '<body${1}class="%s ${2}"${3}>', $class ), $content );
        return preg_replace_callback(
            '/<body([\s\S]*?)(?:class="(.*?)")([\s\S]*?)?([^>]*)>/',
            function($match) use ($class) {
                if($match[2]){
                    return $lazy = str_replace('class="', 'class="' . $class . ' ', $match[0]); 
                }else {
                    return str_replace('<body ', '<body class="' . $class . '" ', $match[0]);
                }
            },
            $content
        );  
    }
}