<?php

namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;
use Magento\Store\Model\ScopeInterface;

class RatingFilter implements DataMapperInterface
{
    const FIELD_NAME = 'rating_summary';

    private $lreviewFactory;

    private $lproductFactory;

    private $scopeConfig;


    public function __construct(
        \Magento\Review\Model\ReviewFactory $lreviewFactory,
        \Magento\Catalog\Model\ProductFactory $lproductFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->lreviewFactory = $lreviewFactory;
        $this->lproductFactory = $lproductFactory;
        $this->scopeConfig = $scopeConfig;
    }

   
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        
        $lproduct = $this->lproductFactory->create(['data' => ['entity_id' => $entityId]]);

        $this->lreviewFactory->create()->getEntitySummary($lproduct, $storeId);
        return [self::FIELD_NAME => $lproduct->getRatingSummary()->getRatingSummary()];
    }

    
    public function isAllowed()
    {
         $rating_filter_config=$this->scopeConfig->getValue('layerednavigation/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         if($rating_filter_config['enabled'])
         {
             return true;
         }
         else
         {
             return false;
         }      
      
    }
}
