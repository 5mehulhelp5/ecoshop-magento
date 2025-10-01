<?php

namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\BucketBuilder;

use Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\BucketBuilderInterface as BucketBuilderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;


class RatingFilter implements BucketBuilderInterface
{
   
    public function build(
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        $qvalues = [];
        foreach ($queryResult['aggregations'][$bucket->getName()]['buckets'] as $qresultBucket) {
            $qkey = (int)floor($qresultBucket['key'] / 20);
            $previousCount = isset($qvalues[$qkey]['count']) ? $qvalues[$qkey]['count'] : 0;
            $qvalues[$qkey] = [
                'value' => $qkey,
                'count' => $qresultBucket['doc_count'] + $previousCount,
            ];
        }
        return $qvalues;
    }
}
