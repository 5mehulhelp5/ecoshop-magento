<?php
namespace Magebees\Layerednavigation\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Brandinfo extends \Magento\Catalog\Block\Product\AbstractProduct
{
    
    protected $brands;
    protected $helper;
    protected $request;
    protected $urlHelper;
    protected $productCollectionFactory;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $request,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
    
        $this->urlHelper = $urlHelper;
        $this->brands = $brands;
        $this->helper = $helper;
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_scopeConfig=$context->getScopeConfig();
        parent::__construct($context, $data);
    }
    public function _toHtml()
    {
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                if ($this->getTemplate()) {
                    $template=$this->getTemplate();
                    $this->setTemplate($template);
                }
                return parent::_toHtml();
            } else {
                return parent::_toHtml();
            }
        } else {
             return parent::_toHtml();
        }
    }
    
    public function _getallbrandCollection()
    {
        $alphabet = $this->getbrandnameAlphabetically();
        $allbrandCollection = [];
     
        foreach ($alphabet as $row) :
                $this->_collection = $this->brands->getCollection();
                $sortorder = $this->getSortorder();
            if ($sortorder == 0) {
                $allbrandCollection[$row] = $this->_collection ->addFieldToFilter('brand_name', ['like' => $row.'%'])->addFieldToFilter('brand_code', $this->getBrandAttributeCode())->addFieldToFilter('status', '1')->setOrder('brand_name', 'asc')->getData();
            } else {
                $allbrandCollection[$row] = $this->_collection ->addFieldToFilter('brand_name', ['like' => $row.'%'])->addFieldToFilter('brand_code', $this->getBrandAttributeCode())->addFieldToFilter('status', '1')->setOrder('sort_order', 'asc')->getData();
            }
        endforeach;
        return $allbrandCollection;
    }
    public function getbrandnameAlphabetically()
    {
        $this->_collection = $this->brands->getCollection()->addFieldToFilter('brand_code', $this->getBrandAttributeCode())->addFieldToFilter('status', '1')->setOrder('brand_name', 'asc');
        $brandcollection= [];
         
        foreach ($this->_collection->getData() as $brandCol) {
            $brnm= substr($brandCol['brand_name'], 0, 1);
            $brnm = strtoupper($brnm);
            if (!array_key_exists(trim($brnm), $brandcollection)) {
                $brandcollection[trim($brnm)]=$brnm;
            }
        }
        return $brandcollection;
    }

    public function getBrandCollection()
    {
        $branddata = $this->brands->getCollection()
        ->addFieldToFilter('brand_code', $this->getBrandAttributeCode())
        ->addFieldToFilter('status', '1')
        ->addFieldToFilter('featuredbrand', '1')
        ->setOrder('sort_order', 'asc');
        return $branddata;
    }
}
