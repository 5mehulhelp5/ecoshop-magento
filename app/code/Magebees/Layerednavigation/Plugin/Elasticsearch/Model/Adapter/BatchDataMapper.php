<?php

namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter;


class BatchDataMapper
{
   
    private $dataMappers = [];
    public function __construct(array $dataMappers = [])
    {
        $this->dataMappers = $dataMappers;
    }

    public function aroundMap(
        $subject,
        callable $proceed,
        array $documentData,
        $storeId,
        $context = []
    ) {
        $documentData = $proceed($documentData, $storeId, $context);
        foreach ($documentData as $lproductId => $ldocument) {
            $context['document'] = $ldocument;
            foreach ($this->dataMappers as $dmapper) {
                if ($dmapper instanceof DataMapperInterface && $dmapper->isAllowed()) {
                    $ldocument = array_merge($ldocument, $dmapper->map($lproductId, $ldocument, $storeId, $context));
                }
            }
            $documentData[$lproductId] = $ldocument;
        }

        return $documentData;
    }
}
