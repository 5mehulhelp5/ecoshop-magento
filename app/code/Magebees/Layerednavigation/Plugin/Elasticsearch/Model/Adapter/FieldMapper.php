<?php
namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter;


class FieldMapper
{
    const ELASTIC_ES_DATA_TYPE_STRING = 'string';
    const ELASTIC_ES_DATA_TYPE_FLOAT = 'float';
    const ELASTIC_ES_DATA_TYPE_INT = 'integer';
    const ELASTIC_ES_DATA_TYPE_DATE = 'date';    
    const ELASTIC_ES_DATA_TYPE_ARRAY = 'array';
    private $fields = [];
    private $customerSession;
    private $storeManager;
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $fields = []
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->fields = $fields;
    }

   
    public function afterGetAllAttributesTypes($subject, array $result)
    {
        foreach ($this->fields as $qfieldName => $qfieldType) {
            if (is_object($qfieldType) && ($qfieldType instanceof AdditionalFieldMapperInterface)) {
                $attributeTypes = $qfieldType->getAdditionalAttributeTypes();
                $result = array_merge($result, $attributeTypes);
                continue;
            }

            if (empty($qfieldName)) {
                continue;
            }
            if ($this->isValidFieldType($qfieldType)) {
                $result[$qfieldName] = ['type' => $qfieldType];
            }
        }

        return $result;
    }

    
    public function aroundGetFieldName($subject, callable $proceed, $qattributeCode, $context = [])
    {
        if (isset($this->fields[$qattributeCode]) && is_object($this->fields[$qattributeCode])) {
            $qfiledMapper = $this->fields[$qattributeCode];
            if ($qfiledMapper instanceof AdditionalFieldMapperInterface) {
                return $qfiledMapper->getFiledName($context);
            }
        }
        return $proceed($qattributeCode, $context);
    }

    private function isValidFieldType($fieldType)
    {
        switch ($fieldType) {
            case self::ELASTIC_ES_DATA_TYPE_STRING:
            case self::ELASTIC_ES_DATA_TYPE_DATE:
            case self::ELASTIC_ES_DATA_TYPE_INT:
            case self::ELASTIC_ES_DATA_TYPE_FLOAT:
                break;
            default:
                $fieldType = false;
                break;
        }
        return $fieldType;
    }
}
