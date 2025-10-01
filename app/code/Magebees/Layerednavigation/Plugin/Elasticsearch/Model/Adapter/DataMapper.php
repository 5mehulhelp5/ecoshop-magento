<?php
namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter;
class DataMapper
{
   
    private $dataMappers = [];
    public function __construct(array $dataMappers = [])
    {
        $this->dataMappers = $dataMappers;
    }   
    public function aroundMap(
        $subject,
        callable $proceed,
        $productId,
        array $indexData,
        $storeId,
        $context = []
    ) {
        $dataDocument = $proceed($productId, $indexData, $storeId, $context);
        $context['document'] = $dataDocument;
        foreach ($this->dataMappers as $dmapper) {
            if ($dmapper instanceof DataMapperInterface && $dmapper->isAllowed()) {
                $dataDocument = array_merge($dataDocument, $dmapper->map($productId, $indexData, $storeId, $context));
            }
        }

        return $dataDocument;
    }
}
