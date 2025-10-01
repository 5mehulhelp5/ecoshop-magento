<?php
namespace Magebees\Layerednavigation\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class BrandProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    
    /** Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * Product collection model
     *
     * @var Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_productCollection;

    /**
     * Catalog Layer
     *
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $_catalogLayer;
    protected $brands;
    protected $helper;
    protected $request;
    protected $_categoryFactory;
    protected $pageConfig;
   
   
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->brands = $brands;
        $this->helper = $helper;
        $this->request =$context->getRequest();
        $this->_categoryFactory = $categoryFactory;
        $this->pageConfig =$context->getPageConfig();
        
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);

        $this->pageConfig->getTitle()->set(__($this->getPageTitle()));//set Page title
    }

    
    /**
     * Get product collection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            $this->_productCollection = $layer->getProductCollection();
        }
        return $this->_productCollection;
    }
    
   
    protected function _prepareLayout()
    {
        $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
     //  $attributeCodeUsed= $this->helper->getBrandAttributeCode()->getData('attribute_code');
       // $option_id=$this->getBrandCollection();
      //  if($option_id)
      //  {

         // $this->_productCollection = $layer->getProductCollection()->addFieldToFilter($attributeCodeUsed,$option_id);   
     //   }
        $url1 = $this->request->getParam('brand_url');
        $url = $this->brands->getCollection()
                    ->addFieldToFilter('seo_url', ['eq'=>$url1]);
        $brand=$url->getData();
        

        if (isset($brand['0'])) {
            $queryName = $brand['0']['option_id'];
             $this->_productCollection = $layer->getProductCollection()->addFieldToFilter($brand['0']['brand_code'],$brand['0']['option_id']);  
           //   $this->_productCollection = $layer->getProductCollection()->addAttributeToFilter(
     // [
      // ['attribute' => $brand['0']['brand_code'], 'like' => '%'.$queryName.'%'],
     // ]);  
            $brandname=$brand['0']['brand_name'];
            $this->pageConfig->getTitle()->set($brandname);
        }
        return parent::_prepareLayout();
    }
    
   public function getBrandCollection()
    {
        $layer = $this->getLayer();
        $url1 = $this->request->getParam('brand_url');
        $attributeCodeUsed= $this->helper->getBrandAttributeCode()->getData('attribute_code');
        $url2 = $this->request->getParam($attributeCodeUsed);
        $option_id='';
        if ($url1 != "") {
            $url = $this->brands->getCollection()
                    ->addFieldToFilter('seo_url', ['eq'=>$url1]);
            $option_id='';
            foreach ($url as $d) {
                $option_id=$d->getData('option_id');
                break;
            }
        }
        if ($url1 == "" && $url2 != '') {
            if ($this->helper->getBrandAttributeCode()->getData('attribute_code') != "attributeNotExist") {
                $attributeCodeParam= $this->request->getParam($this->helper->getBrandAttributeCode()->getData('attribute_code'));
                $attributecodeValue= $attributeCodeParam[0];
            }
            $url = $this->brands->getCollection()
                    ->addFieldToFilter('option_id', ['eq'=>$attributecodeValue]);
            
            $option_id='';
            foreach ($url as $d) {
                $option_id=$d->getData('option_id');
                break;
            }
        }

        if ($option_id !='' || $url2!='') {
            $advancedsearch_url = $this->helper->getBaseUrl();
            $attrCode=$attributeCodeUsed;
            ;
            $brandquery = $this->request->getQuery()->toArray();

            if ($url->getData('option_id')) {
                if ($attributeCodeUsed=='layernav_brand') {
                    $layer->getProductCollection()->addAttributeToFilter($attributeCodeUsed, $option_id);
                } else {
                    $layer->getProductCollection()->addFieldToFilter($attributeCodeUsed, $option_id);
                }
            } else {
                if ($attributeCodeUsed=='layernav_brand') {
                    $layer->getProductCollection()->addAttributeToFilter($attributeCodeUsed, $url2);
                } else {
                    $layer->getProductCollection()->addFieldToFilter($attributeCodeUsed, $url2);
                }
            }
        }
        return $option_id;
    }
    public function getBrandInfo()
    {
        $attributecodeValue = null; 
        
       
        $branddata = $this->brands->getCollection()
                    ->addFieldToFilter('seo_url', ['eq' =>$this->request->getParam('brand_url')]);
      
    
        return $branddata;
    }
    
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore(); // give the information about current store
    }
}
