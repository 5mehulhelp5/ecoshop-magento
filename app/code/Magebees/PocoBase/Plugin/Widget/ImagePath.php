<?php

namespace Magebees\PocoBase\Plugin\Widget;
use \Magento\Widget\Model\Widget as BaseWidget;
class ImagePath
{
    public function beforeGetWidgetDeclaration(BaseWidget $subject, $type, $params = [], $asIs = true)
    {
	
      	if(key_exists("wd_bgimage", $params)) {

            $url = $params["wd_bgimage"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"',(string) $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_bgimage"] = $url;
                }
            }
        }
        if(key_exists("wd_image_section", $params)) {

            $url = $params["wd_image_section"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"',(string) $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_image_section"] = $url;
                }
            }
        }
		if(key_exists("wd_heading_logo", $params)) {

            $url = $params["wd_heading_logo"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"',(string) $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_heading_logo"] = $url;
                }
            }
        }
		if(key_exists("wd_image", $params)) {

            $url = $params["wd_image"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"',(string) $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_image"] = $url;
                }
            }
        }
		return array($type, $params, $asIs);
    }
}