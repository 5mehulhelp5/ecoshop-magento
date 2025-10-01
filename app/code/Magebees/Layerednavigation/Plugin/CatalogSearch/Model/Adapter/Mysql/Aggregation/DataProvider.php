<?php

namespace Magebees\Layerednavigation\Plugin\CatalogSearch\Model\Adapter\Mysql\Aggregation;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Catalog\Model\Product;

class DataProvider
{
    
    protected $dresource;
    protected $scopeResolver;
    protected $productCollFactory;
    protected $productVisibility;
    protected $scopeConfig;
    private $eavConfig;
    public function __construct(
        ResourceConnection $dresource,
        ScopeResolverInterface $scopeResolver,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,      
        \Magento\Eav\Model\Config $eavConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->dresource = $dresource;
        $this->scopeResolver = $scopeResolver;
        $this->productCollFactory = $productCollFactory;
        $this->productVisibility = $productVisibility;       
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
    }

   
    public function aroundGetDataSet(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
        if ($bucket->getField() == 'stock_status') {
            $isStockEnabled =$this->isStockFilterEnabled();
            if ($isStockEnabled) {
                return $this->addStockFilterAggregation($entityIdsTable);
            }
        }

        if ($bucket->getField() == 'rating_summary') {
            $isRatingEnabled = $this->isRatingFilterEnabled();
            if ($isRatingEnabled) {
                return $this->addRatingFilterAggregation($entityIdsTable, $dimensions);
            }
        }

      


        return $proceed($bucket, $dimensions, $entityIdsTable);
    }

    /**
     * @param Table $entityIdsTable
     * @return \Magento\Framework\DB\Select
     */
    protected function addStockFilterAggregation(Table $entityIdsTable)
    {
        $derivedTableName = $this->dresource->getConnection()->select();
        $derivedTableName->from(
            ['main_table' => $this->dresource->getTableName('cataloginventory_stock_status')],
            [
                'value' => 'stock_status',
            ]
        )->joinInner(
            ['entities' => $entityIdsTable->getName()],
            'main_table.product_id  = entities.entity_id',
            []
        )->where('main_table.stock_id = 1');

        $select = $this->dresource->getConnection()->select();
        $select->from(['main_table' => $derivedTableName]);

        return $select;
    }
     public function isStockFilterEnabled()
    {
        $stock_filter_config=$this->scopeConfig->getValue('layerednavigation/stock_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
          if ($stock_filter_config['enabled']) {
            return true;
          }
          else
          {
            return false;
          }
      
    }

    /**
     * @param Table $entityIdsTable
     * @param array $dimensions
     * @return \Magento\Framework\DB\Select
     */
    protected function addRatingFilterAggregation(
        Table $entityIdsTable,
        $dimensions
    ) {
        $currentScope = $dimensions['scope']->getValue();
        $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();
        $derivedTableName = $this->dresource->getConnection()->select();
        $derivedTableName->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $columnRating = new \Zend_Db_Expr("
                IF(main_table.rating_summary >=100,
                    5,
                    IF(
                        main_table.rating_summary >=80,
                        4,
                        IF(main_table.rating_summary >=60,
                            3,
                            IF(main_table.rating_summary >=40,
                                2,
                                IF(main_table.rating_summary >=20,
                                    1,
                                    0
                                )
                            )
                        )
                    )
                )
            ");

        $derivedTableName->joinLeft(
            ['main_table' => $this->dresource->getTableName('review_entity_summary')],
            sprintf(
                '`main_table`.`entity_pk_value`=`entities`.entity_id
                AND `main_table`.entity_type = 1
                AND `main_table`.store_id  =  %d',
                $currentScopeId
            ),
            [
                //'entity_id' => 'entity_pk_value',
                'value' => $columnRating,
            ]
        );
        $select = $this->dresource->getConnection()->select();
        $select->from(['main_table' => $derivedTableName]);
        return $select;
    }
    public function isRatingFilterEnabled()
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
