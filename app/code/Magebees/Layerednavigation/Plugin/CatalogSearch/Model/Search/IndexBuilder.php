<?php

namespace Magebees\Layerednavigation\Plugin\CatalogSearch\Model\Search;

use Magebees\Layerednavigation\Plugin\CatalogSearch\Model\Search\FilterMapper\CustomFilterExclusion;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\BoolExpression;
use Magento\Framework\Search\Request\Query\Filter;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\App\ResourceConnection;

class IndexBuilder
{
    
    protected $scopeConfig;
    protected $storeManager;    
    protected $request;
    protected $resource;   
    private $customExclusionStrategy;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Framework\App\RequestInterface $request,
        ResourceConnection $resource,
      
        CustomFilterExclusion $customExclusionStrategy
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
       $this->request = $request;
        $this->resource = $resource;      
        $this->customExclusionStrategy = $customExclusionStrategy;
    }

  
    public function aroundBuild($subject, callable $proceed, RequestInterface $request)
    {
        $select = $proceed($request);
        $filters = $this->getFilters($request->getQuery());
        foreach ($filters as $filter) {
            $this->customExclusionStrategy->apply($filter, $select);
        }

        if ($this->isEnabledShowOutOfStock() && $this->isEnabledStockFilter()) {
            if ($this->request->getParam('in-stock')) {
                $this->addStockFilterDataToSelect($select);
            }
        }

        

        return $select;
    }

    private function getFilters($query)
    {
        $queryFilters = [];
        switch ($query->getType()) {
            case RequestQueryInterface::TYPE_BOOL:
                /** @var \Magento\Framework\Search\Request\Query\BoolExpression $query */
                foreach ($query->getMust() as $subQuery) {
                    $queryFilters = array_merge($queryFilters, $this->getFilters($subQuery));
                }
                foreach ($query->getShould() as $subQuery) {
                    $queryFilters = array_merge($queryFilters, $this->getFilters($subQuery));
                }
                foreach ($query->getMustNot() as $subQuery) {
                    $queryFilters = array_merge($queryFilters, $this->getFilters($subQuery));
                }
                break;
            case RequestQueryInterface::TYPE_FILTER:
                /** @var Filter $query */
                $filter = $query->getReference();
                if (FilterInterface::TYPE_BOOL === $filter->getType()) {
                    $queryFilters = array_merge($queryFilters, $this->getFiltersFromBoolFilter($filter));
                } else {
                    $queryFilters[] = $filter;
                }
                break;
            default:
                break;
        }
        return $queryFilters;
    }

   
    private function getFiltersFromBoolFilter(BoolExpression $boolExpression)
    {
        $queryFilters = [];
        /** @var BoolExpression $filter */
        foreach ($boolExpression->getMust() as $filter) {
            if ($filter->getType() === FilterInterface::TYPE_BOOL) {
                $queryFilters = array_merge($queryFilters, $this->getFiltersFromBoolFilter($filter));
            } else {
                $queryFilters[] = $filter;
            }
        }
        foreach ($boolExpression->getShould() as $filter) {
            if ($filter->getType() === FilterInterface::TYPE_BOOL) {
                $queryFilters = array_merge($queryFilters, $this->getFiltersFromBoolFilter($filter));
            } else {
                $queryFilters[] = $filter;
            }
        }
        foreach ($boolExpression->getMustNot() as $filter) {
            if ($filter->getType() === FilterInterface::TYPE_BOOL) {
                $queryFilters = array_merge($queryFilters, $this->getFiltersFromBoolFilter($filter));
            } else {
                $queryFilters[] = $filter;
            }
        }
        return $queryFilters;
    }

    /**
     * @param Select $select
     */
    protected function addStockFilterDataToSelect(Select $select)
    {
        $connection = $select->getConnection();

        $select->joinLeft(
            ['stock_status_filter' => $this->resource->getTableName('cataloginventory_stock_status')],
            'search_index.entity_id = stock_status_filter.product_id'
            . $connection->quoteInto(
                ' AND stock_status_filter.website_id IN (?, 0)',
                $this->storeManager->getWebsite()->getId()
            ),
            []
        );
    }

    /**
     * @return bool
     */
    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    protected function isEnabledStockFilter()
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
}
