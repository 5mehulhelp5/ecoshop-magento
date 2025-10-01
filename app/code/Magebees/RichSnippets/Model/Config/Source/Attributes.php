<?php
namespace Magebees\RichSnippets\Model\Config\Source;

class Attributes implements \Magento\Framework\Option\ArrayInterface
{
	 protected $attributeCollection;
	 public function __construct(       
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection
    ) {
        
        $this->attributeCollection=$attributeCollection;
    }
	public function toOptionArray()
	{
		  $attributes=$this->attributeCollection->getData();
		   $attribute_arr['0']='-----None-----';
        	sort($attributes);
		   foreach ($attributes as $attribute) {		  
                $attribute_arr[$attribute['attribute_code']]=$attribute['frontend_label'];               
            
        }
        return $attribute_arr;
		
	}
}