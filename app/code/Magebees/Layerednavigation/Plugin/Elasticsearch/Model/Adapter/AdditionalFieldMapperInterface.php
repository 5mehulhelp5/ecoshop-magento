<?php
namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter;

interface AdditionalFieldMapperInterface
{
    
    public function getAdditionalAttributeTypes();
    public function getFiledName($context);
}
