<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\Elasticsearch\SearchAdapter\Filter\Builder;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\Framework\Search\Request\Filter\Term as TermFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface
    as FieldTypeConverterInterface;
class TermFilter extends \Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term
{
    private $integerTypeAttributes = ['category_ids'];
    protected $fieldMapper;
    protected $attributeAdapterProvider;
    protected $layerHelper;
    protected $_scopeConfig;


    public function __construct(FieldMapperInterface $fieldMapper,  AttributeProvider $attributeAdapterProvider, \Magebees\Layerednavigation\Helper\Data $layerHelper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,  array $integerTypeAttributes = [])
    {
        $this->fieldMapper = $fieldMapper;
		     $this->attributeAdapterProvider = $attributeAdapterProvider;
		 $this->layerHelper = $layerHelper;
		$this->_scopeConfig=$scopeConfig;
		  $this->integerTypeAttributes = array_merge($this->integerTypeAttributes, $integerTypeAttributes);
    }

    /**
     * @param RequestFilterInterface|TermFilterRequest $filter
     * @return array
     */
    public function buildFilter(RequestFilterInterface $filter)
    {
		  $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		  $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		  if ($is_default_enabled==0) {
			if ($is_enabled) {
				$filterQuery = [];	
				if ($filter->getValue()) {
					$operator = is_array($filter->getValue()) ? 'terms' : 'term';
					if(!is_array($filter->getValue())){
						if(strpos($filter->getValue(),',') !== false ) {
							$attribute_code=$filter->getField();
							$attribute=$this->layerHelper->loadAttributeModelByCode($attribute_code);
							if ($this->layerHelper->hasVisualSwatch($attribute)) {
								$and_logic=$this->layerHelper->isApplyAndLogicSwatch($attribute);
							}else {	
								if($attribute_code=='category_ids')
								  {
									   $and_logic=$this->_scopeConfig->getValue('layerednavigation/category_filter/enable_multiselect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
								  }
								  else
								  {					  
									$and_logic=$this->layerHelper->isApplyAndLogic($attribute);
								  }
							} 
								
							$value=explode(',',(string)$filter->getValue());				
							if($and_logic){ 
								 // and condition
								foreach($value as $v){
									$operator='term';
									   $filterQuery []= [
									$operator => [
										$this->fieldMapper->getFieldName($filter->getField()) => $v,
									],
								];										 
								}
							}else {
								  // or condition 
								 $operator='terms';
									 $filterQuery []= [
									$operator => [
										$this->fieldMapper->getFieldName($filter->getField()) => $value,
									],
								];
							 }
						}
						else
						{
								$filterQuery = [];

								$attribute = $this->attributeAdapterProvider->getByAttributeCode($filter->getField());
								$fieldName = $this->fieldMapper->getFieldName($filter->getField());

								if ($attribute->isTextType() && !in_array($attribute->getAttributeCode(), $this->integerTypeAttributes)) {
									$suffix = FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD;
									$fieldName .= '.' . $suffix;
								}

								if ($filter->getValue()) {
									$operator = is_array($filter->getValue()) ? 'terms' : 'term';
									$filterQuery []= [
										$operator => [
											$fieldName => $filter->getValue(),
										],
									];
								}
								return $filterQuery;

						}
					}
					else
					{
							$filterQuery = [];

							$attribute = $this->attributeAdapterProvider->getByAttributeCode($filter->getField());
							$fieldName = $this->fieldMapper->getFieldName($filter->getField());

							if ($attribute->isTextType() && !in_array($attribute->getAttributeCode(), $this->integerTypeAttributes)) {
								$suffix = FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD;
								$fieldName .= '.' . $suffix;
							}

							if ($filter->getValue()) {
								$operator = is_array($filter->getValue()) ? 'terms' : 'term';
								$filterQuery []= [
									$operator => [
										$fieldName => $filter->getValue(),
									],
								];
							}
							return $filterQuery;

					}
				
					
				}
				return $filterQuery;
			}
		}
		
			$filterQuery = [];

			$attribute = $this->attributeAdapterProvider->getByAttributeCode($filter->getField());
			$fieldName = $this->fieldMapper->getFieldName($filter->getField());

			if ($attribute->isTextType() && !in_array($attribute->getAttributeCode(), $this->integerTypeAttributes)) {
				$suffix = FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD;
				$fieldName .= '.' . $suffix;
			}

			if ($filter->getValue()) {
				$operator = is_array($filter->getValue()) ? 'terms' : 'term';
				$filterQuery []= [
					$operator => [
						$fieldName => $filter->getValue(),
					],
				];
			}
			return $filterQuery;

    }
}
