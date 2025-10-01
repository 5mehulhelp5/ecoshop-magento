<?php

namespace Magebees\Layerednavigation\Helper;

use Magebees\Layerednavigation\Model\Cache\Type;
use Magento\Framework\App\Cache;
use Magento\Store\Model\ScopeInterface;

class Attributes extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $optionsSeoData;
    protected $optionsSeoDataLabel;
    protected $optionsSeoDataLen;
    protected $optionsSeoDataSpecialChar;
    protected $optionsSeoDataEncodeChar;
    protected $attributeoptionFactory;
    protected $productAttributeCollectionFactory;
    protected $helper_url;
    protected $_storeManager;
    protected $cache;
    protected $cacheState;
    protected $_catalogeav;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magebees\Layerednavigation\Helper\Url $helper_url,  
        \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Catalog\Model\ResourceModel\Eav\Attribute $catalogeav,  \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        Cache $cache,
        Cache\StateInterface $cacheState
    ) {
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->helper_url = $helper_url;     
        $this->_storeManager = $storeManager;
        $this->cache = $cache;
        $this->cacheState = $cacheState;
		 $this->_catalogeav = $catalogeav;
        parent::__construct($context);
    }
    public function getAllFilterableOptionsAsHash()
    {
        $cache_id = 'magebees_seo_options_alias' .$this->_storeManager->getStore()->getId();
        if (is_null($this->optionsSeoData) && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {             
                $this->optionsSeoData = json_decode($cached,true);
            }
        }
        if (is_null($this->optionsSeoData)) {
            $this->optionsSeoData = [];
        
            $xAttributeValuesUnique = [];
            $attributes = $this->getFilterableAttributes();
            $i=0;
            $dublicate_option_value = [];
			$option_alias = [];
            foreach ($attributes as $a) {				
				  
                $code        = $a['attribute_code'];
                $attr_id       = $a['attribute_id'];
                $options = $this->getStorewiseOptArr($code);       
                $this->optionsSeoData[$code] = [];
                foreach ($options as $o) {
                    $storeId=$this->_storeManager->getStore()->getId();
                    $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                    if (empty($opt_data)) {
                        $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', '0');
                                    $opt_data=$opt_collection->getData();
                        if (isset($opt_data['0'])) {
                                    $option_alias=$opt_data['0']['url_alias'];
                        }
                        else
                        {
                            $option_alias='';
                        }
                                    
                        if ($option_alias) {
                                    $unKey=$option_alias;
                        } else {
                           
							$label_alias=$this->urlAliasAfterReplaceChar($o['label']);                                 
                            if (in_array($label_alias, $this->optionsSeoData[$code])) {
                                $unKey=$label_alias;
                            } else {
                                $unKey=$label_alias;
                            }
                        }
                    } else {
                        $unKey=$this->helper_url->getUrlAlias($o['value'], $attr_id);
                    }
                    if (isset($dublicate_option_value[$unKey])) {
                        $dublicate_option_value[$unKey] = $dublicate_option_value[$unKey]+1;
                    } else {
                        $dublicate_option_value[$unKey] = 1;
                    }
                        $this->optionsSeoData[$code][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];
                        $xAttributeValuesUnique[$unKey] = true;
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            
            $this->cache->save(json_encode($this->optionsSeoData), $cache_id, [Type::CACHE_TAG]);
                
            }
        }
        
    
        return $this->optionsSeoData;
    }
	 public function getAllFilterableOptionsAsHashEncodeChar()
    {
        $cache_id = 'magebees_seo_options_alias_encode_char' .$this->_storeManager->getStore()->getId();
        if (is_null($this->optionsSeoDataEncodeChar) && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {
               
                  $this->optionsSeoDataEncodeChar = json_decode($cached,true);
            }
        }
        if (is_null($this->optionsSeoDataEncodeChar)) {
            $this->optionsSeoDataEncodeChar = [];
        
            $xAttributeValuesUnique = [];
            $attributes = $this->getFilterableAttributes();
            $i=0;
            $dublicate_option_value = [];
            foreach ($attributes as $a) {				
				  
                $code        = $a['attribute_code'];
                $attr_id       = $a['attribute_id'];
                $options = $this->getStorewiseOptArr($code);       
                $this->optionsSeoDataEncodeChar[$code] = [];
                foreach ($options as $o) {
                    $storeId=$this->_storeManager->getStore()->getId();
                    $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                    if (empty($opt_data)) {
                        $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', '0');
                                    $opt_data=$opt_collection->getData();
                        if (isset($opt_data['0'])) {
                                    $option_alias=$opt_data['0']['url_alias'];
                        }
                        else
                        {
                            $option_alias='';
                        }
                                    
                        if ($option_alias) {
                                    $unKey=$option_alias;
                        } else {
                           
							$label_alias=$this->urlAliasAfterReplaceChar($o['label']);                                 
                            if (in_array($label_alias, $this->optionsSeoDataEncodeChar[$code])) {
                                $unKey=$label_alias;
                            } else {
                                $unKey=$label_alias;
                            }
                        }
                    } else {
                        $unKey=$this->helper_url->getUrlAlias($o['value'], $attr_id);
                    }
					$unKey=html_entity_decode($unKey);	
					//$unKey=htmlentities($unKey,ENT_QUOTES,"UTF-8");
				//$unKey =htmlspecialchars($unKey, ENT_QUOTES);
					
					$result=preg_match('/[^\w\s]+/u',$unKey);		
                    if (isset($dublicate_option_value[$unKey])) {
                        $dublicate_option_value[$unKey] = $dublicate_option_value[$unKey]+1;
                    } else {
                        $dublicate_option_value[$unKey] = 1;
                    }
					
					
					$decode_attr_option=$unKey.$this->getOptionSufix($dublicate_option_value[$unKey]);		

				
					
                        $this->optionsSeoDataEncodeChar[$code][$decode_attr_option] =$o['value'];
                        $xAttributeValuesUnique[$unKey] = true;
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
               
                $this->cache->save(json_encode($this->optionsSeoDataEncodeChar), $cache_id, [Type::CACHE_TAG]);
                
            }
        }
        
    
        return $this->optionsSeoDataEncodeChar;
    }
	 public function getAllFilterableOptionsAsHashChar()
    {
          $cache_id = 'magebees_seo_options_alias_char' .$this->_storeManager->getStore()->getId();
        if (is_null($this->optionsSeoDataSpecialChar) && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {
               
                 $this->optionsSeoDataSpecialChar = json_decode($cached,true);
            }
        }
        if (is_null($this->optionsSeoDataSpecialChar)) {
            $this->optionsSeoDataSpecialChar = [];
        
            $xAttributeValuesUnique = [];
            $attributes = $this->getFilterableAttributes();
            $i=0;
            $dublicate_option_value = [];
            foreach ($attributes as $a) {
                $code        = $a['attribute_code'];
                $attr_id       = $a['attribute_id'];
                $options = $this->getStorewiseOptArr($code);
            
            /*demo issue fix 8_4*/
               // $this->optionsSeoDataSpecialChar[$code] = [];
                foreach ($options as $o) {
                    $storeId=$this->_storeManager->getStore()->getId();
                    $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                    if (empty($opt_data)) {
                        $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', '0');
                                    $opt_data=$opt_collection->getData();
                        if (isset($opt_data['0'])) {
                                    $option_alias=$opt_data['0']['url_alias'];
                        }
                        else
                        {
                            $option_alias='';
                        }
                                    
                        if ($option_alias) {
                                    $unKey=$option_alias;
                        } else {                 
                            
        $label_alias=$this->urlAliasAfterReplaceChar($o['label']);
                            if (in_array($label_alias, $this->optionsSeoDataSpecialChar[$code])) {
                                $unKey=$label_alias;
                            } else {
                                $unKey=$label_alias;
                            }
                        }
                    } else {
                        $unKey=$this->helper_url->getUrlAlias($o['value'], $attr_id);
                    }
                    $unKey=html_entity_decode($unKey);  
                    //$unKey=urldecode($unKey); 
                    //$unKey=htmlentities($unKey,ENT_QUOTES,"UTF-8");
                    //$unKey =htmlspecialchars($unKey, ENT_QUOTES);
                    if (isset($dublicate_option_value[$unKey])) {
                        $dublicate_option_value[$unKey] = $dublicate_option_value[$unKey]+1;
                    } else {
                        $dublicate_option_value[$unKey] = 1;
                    }
                    
                    $decode_attr_option=$unKey.$this->getOptionSufix($dublicate_option_value[$unKey]);  

                    $keyLength = strlen($decode_attr_option);
                    if(isset($this->optionsSeoDataSpecialChar[$keyLength]))
                    {
                        $decode_attr_option=$unKey.$this->getOptionSufix($dublicate_option_value[$unKey]);              
                    $this->optionsSeoDataSpecialChar[$keyLength][$code][$decode_attr_option] =$o['value'];
                    }
                    else{
                        $decode_attr_option=$unKey.$this->getOptionSufix($dublicate_option_value[$unKey]);              
            $this->optionsSeoDataSpecialChar[$keyLength][$code][$decode_attr_option] =$o['value'];
                    
                    }
                    
                        /*$this->optionsSeoDataLen[$code][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];*/
                        $xAttributeValuesUnique[$unKey] = true;
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
              
                $this->cache->save(json_encode($this->optionsSeoDataSpecialChar), $cache_id, [Type::CACHE_TAG]);

                
            }
        }

 
        $this->optionsSeoDataSpecialChar = array_filter($this->optionsSeoDataSpecialChar);

 
    
        return $this->optionsSeoDataSpecialChar;
    }
	 public function getAllFilterableOptionsAsHashLabel()
    {
        $cache_id = 'magebees_seo_options_alias_label' .$this->_storeManager->getStore()->getId();		
       if (is_null($this->optionsSeoDataLabel) && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {
               
                 $this->optionsSeoDataLabel = json_decode($cached,true);
            }
        }
        if (is_null($this->optionsSeoDataLabel)) {
			
            $this->optionsSeoDataLabel = [];
        
            $xAttributeValuesUnique = [];
            $attributes = $this->getFilterableAttributes();
            $i=0;
            $dublicate_option_value = [];
			
            foreach ($attributes as $a) {
				
                $code        = $a['attribute_code'];
                $attr_id       = $a['attribute_id'];
				$attrExist = $this->_catalogeav->loadByCode('catalog_product', $a['attribute_code']);	
				$attr_label=$attrExist->getStoreLabel();
               
				
		$attr_label=$this->urlAliasAfterReplaceChar($attr_label);
				
                $options = $this->getStorewiseOptArr($code); 
				
                $this->optionsSeoDataLabel[$attr_label] = [];
				
                foreach ($options as $o) {
                    $storeId=$this->_storeManager->getStore()->getId();
                    $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                    if (empty($opt_data)) {
                        $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', '0');
                                    $opt_data=$opt_collection->getData();
                        if (isset($opt_data['0'])) {
                                    $option_alias=$opt_data['0']['url_alias'];
                        }
                        else
                        {
                            $option_alias='';
                        }
                                    
                        if ($option_alias) {
                                    $unKey=$option_alias;
                        } else {
                          
	$label_alias=$this->urlAliasAfterReplaceChar($o['label']);
                                            
                            if (in_array($label_alias, $this->optionsSeoDataLabel[$attr_label])) {
                                $unKey=$label_alias;
                            } else {
                                $unKey=$label_alias;
                            }
                        }
                    } else {
                        $unKey=$this->helper_url->getUrlAlias($o['value'], $attr_id);
                    }
                    if (isset($dublicate_option_value[$unKey])) {
                        $dublicate_option_value[$unKey] = $dublicate_option_value[$unKey]+1;
                    } else {
                        $dublicate_option_value[$unKey] = 1;
                    }
                        $this->optionsSeoDataLabel[$attr_label][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];
                        $xAttributeValuesUnique[$unKey] = true;
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {            
                   $this->cache->save(json_encode($this->optionsSeoDataLabel), $cache_id, [Type::CACHE_TAG]);
                
            }
        }
        

       $this->optionsSeoDataLabel = array_filter($this->optionsSeoDataLabel);


 

        return $this->optionsSeoDataLabel;
    }
    public function getAllFilterableOptionsAsHashLen()
    {
        $cache_id = 'magebees_seo_options_alias_len' .$this->_storeManager->getStore()->getId();
        if (is_null($this->optionsSeoDataLen) && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {               
                $this->optionsSeoDataLen = json_decode($cached,true);
            }
        }
        if (is_null($this->optionsSeoDataLen)) {
            $this->optionsSeoDataLen = [];
        
            $xAttributeValuesUnique = [];
            $attributes = $this->getFilterableAttributes();
            $i=0;
            $dublicate_option_value = [];
            foreach ($attributes as $a) {
                $code        = $a['attribute_code'];
                $attr_id       = $a['attribute_id'];
                $options = $this->getStorewiseOptArr($code);
            
            
                //$this->optionsSeoDataLen[$code] = [];
                foreach ($options as $o) {
                    $storeId=$this->_storeManager->getStore()->getId();
                    $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                    if (empty($opt_data)) {
                        $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $o['value'])
                                    ->addFieldToFilter('store_id', '0');
                                    $opt_data=$opt_collection->getData();
                        if (isset($opt_data['0'])) {
                                    $option_alias=$opt_data['0']['url_alias'];
                        }
                        else
                        {
                            $option_alias='';
                        }
                                    
                        if ($option_alias) {
                                    $unKey=$option_alias;
                        } else {                 
							
		$label_alias=$this->urlAliasAfterReplaceChar($o['label']);
                            if (in_array($label_alias, $this->optionsSeoDataLen[$code])) {
                                $unKey=$label_alias;
                            } else {
                                $unKey=$label_alias;
                            }
                        }
                    } else {
                        $unKey=$this->helper_url->getUrlAlias($o['value'], $attr_id);
                    }
                    if (isset($dublicate_option_value[$unKey])) {
                        $dublicate_option_value[$unKey] = $dublicate_option_value[$unKey]+1;
                    } else {
                        $dublicate_option_value[$unKey] = 1;
                    }
					$keyLength = strlen($unKey.$this->getOptionSufix($dublicate_option_value[$unKey]));
					if(isset($this->optionsSeoDataLen[$keyLength]))
					{
					$this->optionsSeoDataLen[$keyLength][$code][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];
					}
					else{
			$this->optionsSeoDataLen[$keyLength][$code][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];
					
					}
					
                        /*$this->optionsSeoDataLen[$code][$unKey.$this->getOptionSufix($dublicate_option_value[$unKey])] =$o['value'];*/
                        $xAttributeValuesUnique[$unKey] = true;
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            
                $this->cache->save(json_encode($this->optionsSeoDataLen), $cache_id, [Type::CACHE_TAG]);
                
            }
        }
        
          $this->optionsSeoDataLen = array_filter($this->optionsSeoDataLen);
        return $this->optionsSeoDataLen;
    }
    public function getFilterableAttributes()
    {
        $attributes = $this->productAttributeCollectionFactory->create();
        $frontend_input=['multiselect','select','price','swatch_visual','swatch_text'];
         $attributes->addFieldToFilter('is_filterable', ['gt' => 0])
        ->addFieldToFilter('is_visible', '1')
        ->addFieldToFilter('frontend_input', ['in' =>$frontend_input]);
        foreach ($attributes as $a) {
            $result[$a->getAttributeId()]  = $a->getData();
        }

        return $result;
    }
    public function getAllOptions($code)
    {
        $arr = [];
        $attributes = $this->productAttributeCollectionFactory->create();
        $attributes->addFieldToFilter('attribute_code', $code);
        foreach ($attributes as $a) {
            $options=$a->setStoreId('0')->getSource()->getAllOptions(false);
              //remove first empty key n value
             // array_shift($options);
            foreach ($options as $key => $val) {
                $arr[$key]=$val;
            }
        }
          return $arr;
    }
    public function getStorewiseOptArr($code)
    {
        $storeid=$this->_storeManager->getStore()->getId();
         $arr = [];
        $attributes = $this->productAttributeCollectionFactory->create();
        $attributes->addFieldToFilter('attribute_code', $code);
        foreach ($attributes as $a) {
            $options=$a->setStoreId($storeid)->getSource()->getAllOptions(false);
              //remove first empty key n value
             // array_shift($options);
            foreach ($options as $key => $val) {
                $arr[$key]=$val;
            }
        }
          return $arr;
    }
    public function getAllOptionsById($id)
    {
        $arr = [];
        $attributes = $this->productAttributeCollectionFactory->create();
        $attributes->addFieldToFilter('additional_table.attribute_id', $id);
        foreach ($attributes as $a) {
            $options=$a->setStoreId('0')->getSource()->getAllOptions(false);
            //remove first empty key n value
            // array_shift($options);
            foreach ($options as $key => $val) {
                $arr[$key]=$val;
            }
        }
          return $arr;
    }
    public function getOptionLabelStorewise($attr_id, $store_id, $opt_id)
    {
        $arr = [];
        $attributes = $this->productAttributeCollectionFactory->create();
        $attributes->addFieldToFilter('additional_table.attribute_id', $attr_id);
        foreach ($attributes as $a) {
            $options=$a->setStoreId($store_id)->getSource()->getAllOptions(false);
            
            //remove first empty key n value
              
            // array_shift($options);
             
            foreach ($options as $key => $val) {
                if ($opt_id==$val['value']) {
                     return $val['label'];
                }
            }
        }
    }
    public function getOptionSufix($count)
    {
        $sufix = "";
        for ($i=1; $i<$count; $i++) {
         //   $sufix .= "-";
            $sufix .= $i;
        }
        return $sufix;
    }
	public function getReplaceSpecialChar()
	{
		
		 $seoConfig = $this->scopeConfig->getValue('layerednavigation/seo_setting', ScopeInterface::SCOPE_STORE);	
		
		if($seoConfig['replace_char']==0)
		{
			$replaceChar='-';
		}
		else
		{
			$replaceChar='_';
		}
		return $replaceChar;
	}
	public function IsReplaceAllSpecialChar()
	{
		$seoConfig = $this->scopeConfig->getValue('layerednavigation/seo_setting', ScopeInterface::SCOPE_STORE);
		return $seoConfig['replace_char_all'];
		
	}
	public function urlAliasAfterReplaceChar($option_label)
	{
		
		$replaceChar=$this->getReplaceSpecialChar();		
		$isReplaceAllChar=$this->IsReplaceAllSpecialChar();
		
		if($isReplaceAllChar==0)
		{        
		$label_alias = preg_replace('/[^\w\s]+/u',$replaceChar, $option_label);
		//$label_alias = preg_replace('/[^a-zA-Z0-9]/s',$replaceChar, $option_label);
		$label_alias =preg_replace('/_+/',$replaceChar, $label_alias);
		$option_label=$label_alias;
		}
		$label_alias = str_replace(' ',$replaceChar, $option_label);
		$label_alias = preg_replace('/'.$replaceChar.'+/',$replaceChar, $label_alias);
		//$label_alias = str_replace('/',$replaceChar, $label_alias);
		$label_alias=strtolower($label_alias);
		return $label_alias;
	}
}
