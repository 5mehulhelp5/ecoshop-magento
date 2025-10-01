<?php
namespace Magebees\Finder\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_SEARCH_ENGINE_PATH = 'catalog/search/engine';
    protected $request;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }
    

    public function getCurrentSearchEngine() {
         $currentEngine = $this->scopeConfig->getValue(self::CONFIG_SEARCH_ENGINE_PATH,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $currentEngine;
    }

    public function IsElasticSearch() {
        $currentEngine=$this->getCurrentSearchEngine(); 
        if(($currentEngine=='elasticsearch6')||($currentEngine=='elasticsearch5')||($currentEngine=='elasticsearch'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getFinderId($path)
    {
        $finderparams = [];
        $path = trim($path, '/');
        $finderparams = explode('/', $path);
        if (array_key_exists(1, $finderparams)) {
            $finderId = $finderparams[1];
        } else {
            return 0;
        }
        return $finderId;
    }
    
    public function resetFinder($path)
    {
        $finderparams = [];
        $path = trim($path, '/');
        $finderparams = explode('/', $path);
        if (array_key_exists('2', $finderparams)) {
            return true;
        } else {
            return false;
        }
    }
}
