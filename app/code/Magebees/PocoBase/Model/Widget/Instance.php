<?php 
namespace Magebees\PocoBase\Model\Widget;
class Instance extends \Magento\Widget\Model\Widget\Instance
{
	
	public function beforeSave()
    {       	
		parent::beforeSave();
		$params = $this->getWidgetParameters();
		if(key_exists("wd_bgimage", $params)) {

            $url = $params["wd_bgimage"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', (string)$url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_bgimage"] = $url;
                }
            }
			$res = json_encode($params);
			$this->setWidgetParameters($res);
		}
		if(key_exists("wd_heading_logo", $params)) {

            $url = $params["wd_heading_logo"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', (string)$url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_heading_logo"] = $url;
                }
            }
			$res = json_encode($params);
			$this->setWidgetParameters($res);
		}
		if(key_exists("wd_image", $params)) {

            $url = $params["wd_image"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', (string)$url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', (string)$url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_image"] = $url;
                }
            }
			$res = json_encode($params);
			$this->setWidgetParameters($res);
		}
	}
}