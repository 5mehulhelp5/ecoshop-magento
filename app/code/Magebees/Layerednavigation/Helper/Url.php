<?php
namespace Magebees\Layerednavigation\Helper;
class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $attributeoptionFactory;
    protected $_objectManager;
    protected $_storeManager;
    protected $productAttributeCollectionFactory;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
    ) {
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->_objectManager=$objectManager;
        $this->_storeManager=$storeManager;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }
    public function _formatAttributePartMultilevel($attrCode, $ids)
    {
        

        $options = $this->getAllFilterableOptionsAsHash();
        
        $part    = $this->_convertIdToKeys($options[$attrCode], $ids);

        if (!$part) {
            return '';
        }
        
        return $part;
    }
    private function _convertIdToKeys($options, $ids)
    {
        $options = array_flip($options);

        $keys = [];
        $ids = is_array($ids) ? $ids : explode(',', (string)$ids);
        foreach ($ids as $optionId) {
            if (isset($options[$optionId])) {
                $keys[] = $options[$optionId];
            }
        }
        return $keys;
    }
    public function getAllFilterableOptionsAsHash()
    {
        $attrHelper = $this->_objectManager->create('Magebees\Layerednavigation\Helper\Attributes');
       // return $attrHelper->getAllFilterableOptionsAsHash();
        return $attrHelper->getAllFilterableOptionsAsHash();
    }
    public function checkAddSuffix($url)
    {

        return $url.'.html';
    }
    public function getUrlAlias($option_id, $attr_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $option_id)
                                    ->addFieldToFilter('store_id', $storeId);
                                $opt_data=$opt_collection->getData();
        if (empty($opt_data)) {
                                $arr = [];
            $attributes = $this->productAttributeCollectionFactory->create();
            $attributes->addFieldToFilter('additional_table.attribute_id', $attr_id);
            foreach ($attributes as $a) {
                $options=$a->setStoreId($storeId)->getSource()->getAllOptions(false);
                  $code= $a['attribute_code'];
               
                $opt_id_arr= array_column($options, 'value');
                if (in_array($option_id, $opt_id_arr)) {
                      $opt_arr=[];
                      $hash=$this->getAllFilterableOptionsAsHash();
                      $opt_arr=$hash[$code];
                      $opt_arr=array_flip($opt_arr);
                    if (isset($opt_arr[$option_id])) {
                        $url_alias=$opt_arr[$option_id];
                    } else {
                        //return "not ok";
                    }
                      //$url_alias=$opt_arr[$option_id];
                }
            }
            if (isset($url_alias)) {
                return $url_alias ;
            } else {
                $opt_collection = $option_model->getCollection()
                ->addFieldToFilter('option_id', $option_id)
                ->addFieldToFilter('store_id', '0');
                $opt_data=$opt_collection->getData();
				$option_alias = '';
				if(!empty($opt_data)) {
                	$option_alias=$opt_data['0']['url_alias'];
				}	
                return $option_alias;
            }
        } else {
            $option_alias=$opt_data['0']['url_alias'];
            return $option_alias;
        }
    }
    public function getMetaTitle($option_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $option_id)
                                    ->addFieldToFilter('store_id', $storeId);
                                $opt_data=$opt_collection->getData();
                                
        if (empty($opt_data['0']['meta_title'])) {
                                $opt_collection = $option_model->getCollection()
            ->addFieldToFilter('option_id', $option_id)
            ->addFieldToFilter('store_id', '0');
            $opt_data=$opt_collection->getData();
        }
        if (isset($opt_data['0']['meta_title'])) {
                                $meta_title=$opt_data['0']['meta_title'];
        } else {
            $meta_title='';
        }
                                
                                return $meta_title;
    }
    public function getMetaDescription($option_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $option_id)
                                    ->addFieldToFilter('store_id', $storeId);
                                $opt_data=$opt_collection->getData();
                                
        if (empty($opt_data['0']['meta_desc'])) {
                                $opt_collection = $option_model->getCollection()
            ->addFieldToFilter('option_id', $option_id)
            ->addFieldToFilter('store_id', '0');
            $opt_data=$opt_collection->getData();
        }
        if (isset($opt_data['0']['meta_desc'])) {
                                $meta_desc=$opt_data['0']['meta_desc'];
        } else {
            $meta_desc='';
        }
                                
                                return $meta_desc;
    }
    public function getMetaKeyword($option_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $option_id)
                                    ->addFieldToFilter('store_id', $storeId);
                                $opt_data=$opt_collection->getData();
                                
        if (empty($opt_data['0']['meta_keyword'])) {
                                $opt_collection = $option_model->getCollection()
            ->addFieldToFilter('option_id', $option_id)
            ->addFieldToFilter('store_id', '0');
            $opt_data=$opt_collection->getData();
        }
                                
        if (isset($opt_data['0']['meta_keyword'])) {
                                $meta_keyword=$opt_data['0']['meta_keyword'];
        } else {
            $meta_keyword='';
        }
                                
                                return $meta_keyword;
    }
    
    /**Get option image for attribute filter ****/
    
    public function getOptionImage($option_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
        $opt_collection = $option_model->getCollection()
                        ->addFieldToFilter('option_id', $option_id)
                        ->addFieldToFilter('store_id', $storeId);
                    $opt_data=$opt_collection->getData();
                
        if (empty($opt_data['0']['option_image'])) {
                    $opt_collection = $option_model->getCollection()
            ->addFieldToFilter('option_id', $option_id)
            ->addFieldToFilter('store_id', '0');
            $opt_data=$opt_collection->getData();
        }
        if (isset($opt_data['0']['option_image'])) {
                    $option_image=$opt_data['0']['option_image'];
        } else {
            $option_image='';
        }
                    
                    return $option_image;
    }
    
    /**Get option image for attribute filter ****/
    
    public function getOptionHoverImage($option_id)
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $option_model=$this->attributeoptionFactory->create();
                    $opt_collection = $option_model->getCollection()
                                    ->addFieldToFilter('option_id', $option_id)
                                    ->addFieldToFilter('store_id', $storeId);
                                $opt_data=$opt_collection->getData();
                                
        if (empty($opt_data['0']['option_hover_image'])) {
                                $opt_collection = $option_model->getCollection()
            ->addFieldToFilter('option_id', $option_id)
            ->addFieldToFilter('store_id', '0');
            $opt_data=$opt_collection->getData();
        }
        if (isset($opt_data['0']['option_hover_image'])) {
                                $option_hover_image=$opt_data['0']['option_hover_image'];
        } else {
            $option_hover_image='';
        }
                                return $option_hover_image;
    }
    
    
    public function getOptionImageMediaDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'layerOption/images';
        return $path;
    }
    public function getOptionHoverMediaDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'layerOptionHover/images';
        return $path;
    }
}
