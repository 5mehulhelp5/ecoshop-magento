<?php
namespace Magebees\RichSnippets\Block;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    protected $_reviewSummaryFactory; 
	protected $reviewCollFactory; 	
	protected $ratingVoteFactory; 	
	protected $_imageBuilder; 	
	protected $_pricingHelper; 	
	protected $helper; 	
	protected $productModel; 	
	protected $timezone; 	
     
public function __construct(\Magento\Catalog\Block\Product\Context $productContext,
    \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory,
    \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollFactory,
    \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $ratingVoteFactory,
    \Magento\Framework\View\Element\Template\Context $context,
     \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection,  
    \Magento\Catalog\Helper\Output $helper,
    \Magento\Catalog\Model\ProductFactory $productModel,
    \Magento\Framework\Pricing\Helper\Data $pricingHelper, 
     \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
     array $data = [])
    {
        $this->_coreRegistry = $productContext->getRegistry();
        $this->reviewCollFactory = $reviewCollFactory;
        $this->_reviewSummaryFactory = $reviewSummaryFactory;$this->attributeCollection=$attributeCollection;
        $this->ratingVoteFactory = $ratingVoteFactory; 
        $this->_imageBuilder = $productContext->getImageBuilder();  
         $this->_pricingHelper = $pricingHelper; 
         $this->helper = $helper;    
         $this->productModel = $productModel; 
         $this->timezone = $timezone;   
        parent::__construct($context, $data);
    }
   
     public function canShowContent()
    {
         $ext_enable=$this->_scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $product_rich_data_enable=$this->_scopeConfig->getValue('richsnippets/product_rich_data/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);   

         if ((!$product_rich_data_enable)||(!$ext_enable)) {
            
            return false;
        }
        $productParameters = array(
             'show_availability'        => $this->_scopeConfig->getValue('richsnippets/product_rich_data/show_availability',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'show_condition'        => $this->_scopeConfig->getValue('richsnippets/product_rich_data/show_condition',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'brand'        => $this->_scopeConfig->getValue('richsnippets/product_rich_data/brand',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'manufacture'        => $this->_scopeConfig->getValue('richsnippets/product_rich_data/manufacture',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'show_rating'        => $this->_scopeConfig->getValue('richsnippets/product_rich_data/show_rating',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),

        );
        if (array_filter($productParameters)) {
            return $productParameters;
        }

        return false;
    }
    public function getReviewCollection($_product)
    {
             
        $currentStoreId =$this->_storeManager->getStore()->getId();
        $rating =$this->reviewCollFactory;
        $collection = $rating->create()->addStoreFilter(
            $currentStoreId
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $_product->getId()
        )->setDateOrder();
        $review_arr=[];
        if($collection->getData())
        {
            foreach ($collection->getData() as $key => $value) {             
            $review_arr[$key]['datePublished']=$value['created_at'];
            $review_arr[$key]['reviewBody']=str_replace('"', '',(string)$value['detail']);
			$review_arr[$key]['name']=str_replace('"', '',(string)$value['title']);
            $review_arr[$key]['author_name']=$value['nickname'];
            $review_arr[$key]['review_id']=$value['review_id'];
            $reviewId=$value['review_id'];
            $ratingCollection = $this->ratingVoteFactory->create()->addRatingInfo()
                       ->addOptionInfo()
                       ->addRatingOptions()
                       ->addFieldToFilter('review_id',$reviewId);  
            $rating_data=$ratingCollection->getData();
            if($ratingCollection->getData())
            {
                $review_arr[$key]['percent']=$rating_data['0']['percent'];
            }
    
            }
        }
        return $review_arr;
    }   
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
    public function getDescription($product)
    {
        $desc_type=$this->_scopeConfig->getValue('richsnippets/product_rich_data/description',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $product=$this->productModel->create()->load($product->getId());
        
        if ($desc_type==0) {
            return false;           
        }elseif ($desc_type==1) {            
           return nl2br(str_replace('"', '',(string)$product->getData('description')));
        } 
        elseif ($desc_type==2) {
          return nl2br(str_replace('"', '',(string)$product->getData('short_description')));
        }
        else{
            return nl2br(str_replace('"', '',(string)$product->getData('meta_description')));           
        } 
    }
    public function getCustomProperties()
    {
        $product_rich_data_property=$this->_scopeConfig->getValue('richsnippets/product_rich_data/custom_property',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);              
        $properties_arr=array();
        if($product_rich_data_property=='')
        {
            return false;
        } 
        $product_rich_data_property = array_unique(preg_split('|[\r\n]+|', $product_rich_data_property, -1, PREG_SPLIT_NO_EMPTY));   
        foreach($product_rich_data_property as $key=>$val)
        {
        	if (strpos($val, ',') !== false) {
  				$data_property_arr=explode(',',(string)$val);
  				$properties_arr[$data_property_arr[0]]=$data_property_arr[1];
			}
			else
			{
				$properties_arr[$val]=$val;
			}
        	
        }   
        return $properties_arr;      

    }
    public function checkCustomPropExist($property)
    {
        $attributes=$this->attributeCollection->getData();
        foreach ($attributes as $attribute) { 
             $attribute_arr[]=$attribute['attribute_code'];
        }        
        if (in_array($property, $attribute_arr))
        {
               return true;
        }
        else{
             return false;
        }
    }
    public function getAttributeDetail($product,$brandAttribute)
    {     
    	$product=$this->productModel->create()->load($product->getId());  
        if($brandAttribute=='sku')
        {
        	return $product->getSku();
        }
        $brandName = '';
        if ($brandAttribute) {
            try {
                
                $brandName = $product->getAttributeText($brandAttribute);
                              
                if (is_array($brandName)) {
                   $brandName=implode(',',(array)$brandName);      
                }
                else
                {
                    $brandName = ''; 
                }
            } catch (\Exception $ex) {
                $brandName = '';
            }
        }

        return $brandName;
    }

    public function getSku($product)
    {
       
        $sku = '';   
            try {
                $sku = $product->getAttributeText($skuAttribute);
                if (is_array($sku) || !$sku) {
                    $sku = $product->getData($skuAttribute);
                }
            } catch (\Exception $ex) {
                $sku = '';
            }
        //}
        return $sku;
    }

    public function getReviewSummary($_product)
    {
      
        $storeId = $this->_storeManager->getStore()->getId();
        $reviewSummary = $this->_reviewSummaryFactory->create();
        $reviewSummary->setData('store_id', $storeId);
        $summaryModel = $reviewSummary->load($_product->getId());

        return $summaryModel;
    }

    
    public function getCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getFrontendName();
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    public function getOfferDetail($_product)
    {
        $product_rich_data_enable=$this->_scopeConfig->getValue('richsnippets/product_rich_data/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $show_config_as=$this->_scopeConfig->getValue('richsnippets/product_rich_data/show_config_as',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $show_group_as=$this->_scopeConfig->getValue('richsnippets/product_rich_data/show_group_as',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);       
        $child_product_arr=array();
		$configProductsPricesArray = array();
        $child_arr=array();
        if(($_product->getTypeId()==='configurable')||($_product->getTypeId()==='grouped'))
        {
            $productType = $_product->getTypeInstance();
            if(($_product->getTypeId()==='configurable'))
            {
              $config_type=$show_config_as;
              $_children = $_product->getTypeInstance()->getUsedProducts($_product);
                
             $assocProducts = $productType->getUsedProductCollection($_product)
                ->addMinimalPrice()
                ->setOrder('minimal_price', 'ASC');
            $childcount=0;
            foreach ($assocProducts as $assocProduct) {
                $childcount++;
                //$configProductsPricesArray[] = $assocProduct->getFinalPrice();
                $configProductsPricesArray[] =$this->getPrice($assocProduct);
            }
            $offerCount=$childcount;
            $min_max_price_arr=$configProductsPricesArray;  
                
            }
            else
            {
               $config_type=$show_group_as; 
                $_children = $_product->getTypeInstance()->getAssociatedProducts($_product);
                $assocProducts = $productType->getAssociatedProductCollection($_product)
                ->addMinimalPrice()
                ->setOrder('minimal_price', 'ASC');
            $childcount=0;
            foreach ($assocProducts as $assocProduct) {
                 $childcount++;
                //$configProductsPricesArray[] = $assocProduct->getFinalPrice();
                $configProductsPricesArray[] = $this->getPrice($assocProduct);
                
            }
            $offerCount=$childcount;
            $min_max_price_arr=$configProductsPricesArray; 
            }
            if($config_type==0)
            {
                $price=$this->getPrice($_product);
                $url=$this->getCurrentUrl($_product);
                $child_arr['validuntil']=$this->checkPriceValidDate($_product);
                $child_arr['price']= $price;
                $child_arr['url']= $url;
                $child_arr['type']= 'Offer';   
                $child_product_arr[]=$child_arr;
            }
            elseif($config_type==1)
            {                      
                        
            foreach ($_children as $child){                
               // $child_price=$child->getPrice();
                $child_price=$this->getPrice($child);
                $child_url=$child->getProductUrl();
                $child_arr['validuntil']=$this->checkPriceValidDate($child);
                $child_arr['price']= $child_price;
                $child_arr['url']= $child_url;
                $child_arr['type']= 'Offer';
                $child_product_arr[]=$child_arr;
             }
            }
            else
            {
               // $price=$this->getPrice($_product);
                $minPrice = $this->getMinPrice($min_max_price_arr);
                $maxPrice = $this->getMaxPrice($min_max_price_arr);
                if($offerCount!='')
                {
                 $child_arr['offerCount']= $offerCount;
                }
                $url=$this->getCurrentUrl($_product);
                $child_arr['validuntil']=$this->checkPriceValidDate($_product);
                $child_arr['minPrice']= $minPrice;
                $child_arr['maxPrice']= $maxPrice;
                $child_arr['url']= $url;
                $child_arr['type']= 'AggregateOffer';    
                $child_product_arr[]=$child_arr;
            }
           
        }
        else
        {
                $price=$this->getPrice($_product);
                $url=$this->getCurrentUrl($_product);
                $child_arr['validuntil']=$this->checkPriceValidDate($_product);
                $child_arr['price']= $price;
                $child_arr['url']= $url;
                $child_arr['type']= 'Offer';
                $child_product_arr[]=$child_arr;
        }

       return $child_product_arr;        
    }
    public function getPrice($_product)
    {
        $store = $this->_storeManager->getStore();
       
       // $price=$this->_pricingHelper->currencyByStore($this->getPriceValues($_product), $store, false, false);
       /* $price=$this->getPriceValues($_product);
        return $price;*/
        $priceOption='excl_tax';      
        return $this->_calculatePrice($priceOption,$_product);
    }
    public function getMinPrice($price)
    {
        $store = $this->_storeManager->getStore();
        $minPrice = $this->_pricingHelper->currencyByStore(min($price), $store, false, false);
        return $minPrice;
    }
    public function getMaxPrice($price)
    {
        $store = $this->_storeManager->getStore();
        $maxPrice = $this->_pricingHelper->currencyByStore(max($price), $store, false, false);
         return $maxPrice;
    }
    public function getPriceValues($product)
    {
      
        $priceModel  = $product->getPriceModel();
        $productType = $product->getTypeInstance();

        /*if ('bundle' === $product->getTypeId()) {
            return $priceModel->getTotalPrices($product);
        }
       */
        $minPrice   = $product->getMinimalPrice();
        $finalPrice = $product->getFinalPrice();
        if ($minPrice && $minPrice < $finalPrice) {
            return array($minPrice, $finalPrice);
        }

        return $finalPrice;
    }
    public function checkPriceValidDate($_product)
    {
        $current_date =$this->timezone->date()->format('Y-m-d');  
        $endDate=$_product->getSpecialToDate();
        if(!$endDate)
        {
            return false;
        }
        $specialPriceEndDate=$this->timezone->date($endDate)->format('Y-m-d');
        if($specialPriceEndDate>=$current_date)
        {
            return $_product->getSpecialToDate();
        }
        return false;
    }
    /**
     * @param string $priceOption
     * @return float
     */
    protected function _calculatePrice($priceOption,$_product) {

         $_product=$this->productModel->create()->load($_product->getId());  
        $current_date =$this->timezone->date()->format('Y-m-d');  
       $endDate=$_product->getSpecialToDate();
       $specialPriceEndDate=$this->timezone->date($endDate)->format('Y-m-d');
       $specialPrice=$_product->getPriceInfo()->getPrice('special_price')->getAmount();
       if($specialPrice)
       {
            if($endDate)
            {
                if($specialPriceEndDate>=$current_date)
                {
                    $priceInfo = $_product->getPriceInfo()->getPrice('special_price')->getAmount();
                }
                else
                {
                   $priceInfo = $_product->getPriceInfo()->getPrice('final_price')->getAmount(); 
                }
            }           
            else
            {
                $priceInfo = $_product->getPriceInfo()->getPrice('final_price')->getAmount();
            }
            
       }
       else
       {
          $priceInfo = $_product->getPriceInfo()->getPrice('final_price')->getAmount();
       }    
       
        $price = $priceInfo->getValue();
        /** Display of both prices incl. tax and excl. tax */
        if ((int)$this->_scopeConfig->getValue(
                'tax/display/type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE) === 3
        ) {
            switch ($priceOption) {
                case 'incl_tax':
                    $price = $priceInfo->getValue();
                    break;
                case 'excl_tax' :
                    $price = $priceInfo->getValue('tax');
                    break;
            }
        }
        return number_format($price, 2, '.', '');
    }


}