<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\ResourceModel\Stock;

use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

/**
 * CatalogInventory Stock Status per website Resource Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Status extends \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
{
    /**
     * Store model manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     * @deprecated
     */
    protected $_storeManager;

    /**
     * Website model factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;
    
    public function addIsOutStockFilterToCollection($collection, $stock_filter)
    {
        
        $websiteId = $this->getStockConfiguration()->getDefaultScopeId();
        $joinCondition = $this->getConnection()->quoteInto(
            'e.entity_id = stock_status_index.product_id' . ' AND stock_status_index.website_id = ?',
            $websiteId
        );

        $joinCondition .= $this->getConnection()->quoteInto(
            ' AND stock_status_index.stock_id = ?',
            Stock::DEFAULT_STOCK_ID
        );
        $method = 'joinLeft';
        $collection->getSelect()->$method(
            ['stock_status_index' => $this->getMainTable()],
            $joinCondition,
            ['is_salable' => 'stock_status']
        );

        if ($stock_filter!=null) {
            $collection->getSelect()->where(
                'stock_status_index.stock_status = ?',
                $stock_filter
            );
        }
        return $collection;
    }
        
    
    /**
     * @return StockConfigurationInterface
     *
     * @deprecated
     */
    private function getStockConfiguration()
    {
        if ($this->stockConfiguration === null) {
            $this->stockConfiguration = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\CatalogInventory\Api\StockConfigurationInterface');
        }
        return $this->stockConfiguration;
    }
}
