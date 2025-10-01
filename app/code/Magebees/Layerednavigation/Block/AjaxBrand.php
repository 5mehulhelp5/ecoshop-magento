<?php

namespace Magebees\Layerednavigation\Block;

class AjaxBrand extends \Magento\Framework\View\Element\Template
{

    protected $brands;
    protected $_request;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Layerednavigation\Model\Brands $brands,
        array $data = []
    ) {
       
        $this->brands = $brands;
        
        $this->_request = $context->getRequest();
        parent::__construct($context, $data);
    }
    
    public function getAjaxBrandData()
    {
        $bnm= $this->_request->getParam('brandName');
        $digitNumber = ['1','2','3','4','5','6','7','8','9','0'];
        
        $sortorder = $this->_scopeConfig->getValue('layerednavigation/setting/sortorder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $brands_config = $this->_scopeConfig->getValue('layerednavigation/brands', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $brand_url_key=$brands_config['brand_url_key'];
        if ($sortorder == 0) {
            $brandlogo_coll = $this->brands->getCollection()->addFieldToFilter('status', '1')->setOrder('brand_name', 'asc');
        } else {
            $brandlogo_coll = $this->brands->getCollection()->addFieldToFilter('status', '1')->setOrder('sort_order', 'asc');
        }
        
        if ($bnm == "ALL") {
            $brandlogo_coll=$brandlogo_coll->getData();
        } elseif ($bnm == "#") {
            $condition = [];
            for ($i = 0; $i<=9; $i++) {
                $condition[] = ['like'=>$i.'%'];
            }
            $brandlogo_coll=$brandlogo_coll->addFieldToFilter('brand_name', $condition);
            $brandlogo_coll=$brandlogo_coll->getData();
        } else {
            $brandlogo_coll = $brandlogo_coll->addFieldToFilter('brand_name', ['like' => $bnm .'%']);
        }
        return $brandlogo_coll;
    }
}
