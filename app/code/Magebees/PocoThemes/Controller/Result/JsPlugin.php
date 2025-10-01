<?php
namespace Magebees\PocoThemes\Controller\Result;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\HTTP\Header;
/**
 * Plugin for processing relocation of javascript
 */
class JsPlugin
{
    /**
     * Request
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
	protected $httpHeader;
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
	protected $_directory_list;
	protected $_filesystem;
	protected $_storeManager;
	protected $_driverfile;

    /**
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Filesystem\Driver\File $driverfile,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		Header $httpHeader
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->_directory_list = $directory_list;
		$this->_filesystem = $filesystem;
		$this->_driverfile = $driverfile;
		$this->_storeManager = $storeManager;
		$this->httpHeader    = $httpHeader;
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
		if (PHP_SAPI === 'cli' || $this->request->isXmlHttpRequest()) {
            return $result;
        }
		
        $html = $response->getBody();
		$scripts = [];
		$scripts_xmagtemplate_bundle = array();
		$scripts_xmagtemplate_bundle_option = array();
		$scripts_xmagtemplate = array();
		$scripts_xmag_init = array();
		$script_ld_json_init = array();
		$scripts_baseurl = array();
		$scripts_src = array();
		$scripts_combine = array();
		$startTag = '<script';
        $endTag = '</script>';
		$strcode = $this->_storeManager->getStore()->getCode(); 
		$strbaseurl =$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $start = 0;
        $i = 0;
		$combinejscodexmag ="";
		$combinejscode = "";
		$tags = 'script';
		$flagstart = 0;
		$dir_permission = 0755;
		$file_permission = 0644;
		$noJs = $this->isNoJs();
		/****  Code to minify and combining CSS links tag ends here ****/
		
		/**** Code to minify and combining script tags starts here  ****/
		$bundle_options_template_script = array();
		$javascript_optimizer_enabled = true;
		$javascript_enable_deferredjs = true;
		if($noJs){
		if($javascript_optimizer_enabled){
			if($javascript_enable_deferredjs){
			while (false !== ($start = stripos($html, $startTag, $start))) {
            $i++;
            if ($i > 1000 ) {
                return $result;
            }
			
            $end = stripos($html, $endTag, $start);
            if (false === $end) {
                break;
            }

            $len = $end + strlen($endTag) - $start;
            $script = substr($html, $start, $len);
			
			// start code to skip bundle summary and bundle option scripts
			
			$script_xmagtemplate_bundle_option = stripos($script, 'data-template="bundle-option"');
			$script_xmagtemplate_bundle_summary = stripos($script, 'data-template="bundle-summary"');
			$script_xmagrole_msrp_popup_template = stripos($script, 'data-role="msrp-popup-template"');
			$script_xmagrole_msrp_info_template = stripos($script, 'data-role="msrp-info-template"');
			
			
			if($script_xmagtemplate_bundle_option){
				
				$bundle_options_template_script['template_bundle_option_'.$i] = $script; 
			
				$html = str_replace($script, 'template_bundle_option_'.$i, $html);
				continue;
			}else if ($script_xmagtemplate_bundle_summary){
				
				$bundle_options_template_script['template_bundle_summary_'.$i] = $script;
				
				$html = str_replace($script, 'template_bundle_summary_'.$i, $html);
				continue;
			}else if ($script_xmagrole_msrp_popup_template){
				
				$bundle_options_template_script['template_bundle_summary_'.$i] = $script;
				
				$html = str_replace($script, 'template_bundle_summary_'.$i, $html);
				continue;
			}else if ($script_xmagrole_msrp_info_template){
				
				$bundle_options_template_script['template_bundle_summary_'.$i] = $script;
				
				$html = str_replace($script, 'template_bundle_summary_'.$i, $html);
				continue;
			}else{
			$html = str_replace($script, '', $html);	
			}
			//$html = str_replace($script, '', $html);	
			// end code to skip bundle summary and bundle option scripts
			
			
			if($flagstart == 0){
				 $script = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $script ); //Yancharuk's code/regex to  remove comments
				$script = preg_replace('/\s+/', ' ', $script);  // to remove all white space
				$scripts_baseurl[] = $script;   
				$flagstart++;
				continue;
			}
		
			$script_src = stripos($script, 'src="');
			
			$script_xmagento = stripos($script, 'text/x-magento-template');
			$script_xmagento_init = stripos($script, 'text/x-magento-init');
			$script_ld_json = stripos($script, 'application/ld+json');
			if($script_src ){
            	
				preg_match_all("/(?<=\<script).*?src=\"([^\"]+)\"/", $script, $all);
				$script_src = implode($all[1]); 
				$scripts_src[] = '<script type="text/javascript" src="'.$script_src.'"  defer="defer"></script>';					
			}else if($script_xmagento)	{
				$scripts_xmagtemplate[] = preg_replace('/\s+/', ' ', $script);     // to remove all white space
				   
            }else if($script_xmagento_init){
				$script = str_replace('<script type="text/x-magento-init">','',$script);
				$script = str_replace('</script>',"",$script);
				$scripts_xmag_init[] = '<script type="text/x-magento-init">' .  json_encode(json_decode($script)) . "</script>";    
				
            }else if($script_ld_json){
				$script = str_replace('<script type="application/ld+json">','',$script);
				$script = str_replace('</script>',"",$script);
				$script_ld_json_init[] = '<script type="application/ld+json">' .  json_encode(json_decode($script)) . "</script>";    
				
            }else{
				$script = preg_replace('#<script(.*?)>#is', '', $script);
				$script = preg_replace('#</script>#is', '', $script);
				
				$combinejscode = 	$combinejscode  . 	$script . ';';

            }
		
        }
		
		
		foreach($bundle_options_template_script as $key => $bundle_script):
			$html = str_replace($key, $bundle_script, $html);	
		endforeach;
		//print_r($bundle_options_template_script);die;
		
		$combinejscode = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $combinejscode ); //Yancharuk's code/regex to remove comments
		 $combinejscode = preg_replace('/\s+/', ' ', $combinejscode);  // to remove all extra white space
			
		//$scripts_combine[] = "<script>" . $combinejscode . "</script>";
			
				
				// start writing js code in file
				
			$combinejscode_file_dir =         $mediaPath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)->getAbsolutePath()."magebees/gpso/compressed/";
			$combinejscode = preg_replace('/\s+/', ' ', $combinejscode); 
			$compressjsfilename= hash('ripemd160', $combinejscode)  . ".min.js";
		
				if(!$this->_driverfile->isExists($combinejscode_file_dir))
				{
				$this->_driverfile->createDirectory($combinejscode_file_dir,$dir_permission);
				}
				if(!$this->_driverfile->isWritable($combinejscode_file_dir))
				{
					$this->_driverfile->changePermissionsRecursively($combinejscode_file_dir,$dir_permission,$file_permission);
				}
			$js_file_path = $combinejscode_file_dir.$compressjsfilename;

			if(!$this->_driverfile->isExists($js_file_path))
			{
				$this->_driverfile->filePutContents($js_file_path,$combinejscode);
			}
				$scripts_combine[] = "<script defer='defer' src='".$strbaseurl."magebees/gpso/compressed/". $compressjsfilename ."'></script>";
			// End writing js code in file	
				
				$scripts = array_merge($scripts_baseurl,$scripts_src,$scripts_combine,$scripts_xmagtemplate,$scripts_xmag_init,$script_ld_json_init);  //set js array according to priority 
				
			
			$scripts = implode(PHP_EOL, $scripts);
			$scripts = null;
				$end = stripos($html, '</body>');
				if ($end) {
				$html = substr($html, 0, $end) . $scripts . substr($html, $end);
				} else {
				$html .= $scripts;
				}
		}
		
		/**** Code to minify and combining scripts tag ends here ****/
		
		}
	}
        $response->setBody($html);
        return $result;
}
public function checkisFileAvailable($file_url){
		$headers = @get_headers($file_url);
   		 return substr($headers[0], 9, 3);
		
	}
		public function isNoJs()
		{
			$userAgent = $this->httpHeader->getHttpUserAgent();
			return preg_match('/Chrome-Lighthouse|PingdomPageSpeed|PingdomPageSpeed|GTmetrix/i', $userAgent);     
		}
		public function rel2abs($base0, $rel0) {
				  // init
				  $base = parse_url($base0);
				  $rel = parse_url($rel0);
				  // init paths so we can blank the base path if we have a rel host
				  if (array_key_exists("path", $rel)) {
					$relPath = $rel["path"];
				  } else {
					$relPath = "";
				  }
				  if (array_key_exists("path", $base)) {
					$basePath = $base["path"];
				  } else {
					$basePath = "";
				  }
				  // if rel has scheme, it has everything
				  if (array_key_exists("scheme", $rel)) {
					return $rel0;
				  }
				  // else use base scheme
				  if (array_key_exists("scheme", $base)) {
					$abs = $base["scheme"];
				  } else {
					$abs = "";
				  }
				  if (strlen($abs) > 0) {
					$abs .= "://";
				  }
				  // if rel has host, it has everything, so blank the base path
				  // else use base host and carry on
				  if (array_key_exists("host", $rel)) {
					$abs .= $rel["host"];
					if (array_key_exists("port", $rel)) {
					  $abs .= ":";
					  $abs .= $rel["port"];
					}
					$basePath = "";
				  } else if (array_key_exists("host", $base)) {
					$abs .= $base["host"];
					if (array_key_exists("port", $base)) {
					  $abs .= ":";
					  $abs .= $base["port"];
					}
				  }
				  // if rel starts with slash, that's it
				  if (strlen($relPath) > 0 && $relPath[0] == "/") {
					return $abs . $relPath;
				  }
				  // split the base path parts
				  $parts = array();
				  $absParts = explode("/", $basePath);
				  foreach ($absParts as $part) {
					array_push($parts, $part);
				  }
				  // remove the first empty part
				  while (count($parts) >= 1 && strlen($parts[0]) == 0) {
					array_shift($parts);
				  }

				  // split the rel base parts
				  $relParts = explode("/", $relPath);
				  if (count($relParts) > 0 && strlen($relParts[0]) > 0) {
					array_pop($parts);
				  }
				  // iterate over rel parts and do the math
				  $addSlash = false;
				  foreach ($relParts as $part) {
					if ($part == "") {
					} else if ($part == ".") {
					  $addSlash = true;
					} else if ($part == "..") {
					  array_pop($parts);
					  $addSlash = true;
					} else {
					  array_push($parts, $part);
					  $addSlash = false;
					}
				  }
				  // combine the result
				  foreach ($parts as $part) {
					$abs .= "/";
					$abs .= $part;
				  }
				  if ($addSlash) {
					$abs .= "/";
				  }
				  if (array_key_exists("query", $rel)) {
					$abs .= "?";
					$abs .= $rel["query"];
				  }

				  if (array_key_exists("fragment", $rel)) {
					$abs .= "#";
					$abs .= $rel["fragment"];
				  }

				  return $abs;
				}    
function minifyCss($css) {
  // some of the following functions to minimize the css-output are directly taken
  // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
  // all credits to Christian Schaefer: http://twitter.com/derSchepp
  // remove comments
  $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
  // backup values within single or double quotes
  preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
  for ($i=0; $i < count($hit[1]); $i++) {
    $css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
  }
  // remove traling semicolon of selector's last property
  $css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);
  // remove any whitespace between semicolon and property-name
  $css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);
  // remove any whitespace surrounding property-colon
  $css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);
  // remove any whitespace surrounding selector-comma
  $css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);
  // remove any whitespace surrounding opening parenthesis
  $css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);
  // remove any whitespace between numbers and units
  $css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);
  // shorten zero-values
  $css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);
  // constrain multiple whitespaces
  $css = preg_replace('/\p{Zs}+/ims',' ', $css);
  // remove newlines
  $css = str_replace(array("\r\n", "\r", "\n"), '', $css);
  // Restore backupped values within single or double quotes
  for ($i=0; $i < count($hit[1]); $i++) {
    $css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
  }
  return $css;
}
}

