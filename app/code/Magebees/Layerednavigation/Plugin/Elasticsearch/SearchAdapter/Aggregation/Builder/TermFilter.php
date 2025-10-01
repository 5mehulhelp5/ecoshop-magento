<?php

namespace Magebees\Layerednavigation\Plugin\Elasticsearch\SearchAdapter\Aggregation\Builder;

use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\BucketBuilderInterface;

class TermFilter
{
   
    private $bucketBuilders = [];
    public function __construct(array $bucketBuilders = [])
    {
        $this->bucketBuilders = $bucketBuilders;
    }

    public function aroundBuild(
        $subject,
        callable $proceed,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        if (isset($this->bucketBuilders[$bucket->getField()])) {
            $termBuilder = $this->bucketBuilders[$bucket->getField()];
            if ($termBuilder instanceof BucketBuilderInterface) {
                return $termBuilder->build($bucket, $dimensions, $queryResult, $dataProvider);
            }
        }
        return $proceed($bucket, $dimensions, $queryResult, $dataProvider);
    }
}
