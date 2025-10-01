<?php 
namespace Magebees\Productlisting\Model\Widget;
class Instance extends \Magento\Widget\Model\Widget\Instance
{
	
	public function beforeSave()
    {       	
		parent::beforeSave();
		$params = $this->getWidgetParameters();
		if(key_exists("wd_bgimage", $params)) {

            $url = $params["wd_bgimage"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["wd_bgimage"] = $url;
                }
            }
			$res = json_encode($params);
			$this->setWidgetParameters($res);
		}
	}
}