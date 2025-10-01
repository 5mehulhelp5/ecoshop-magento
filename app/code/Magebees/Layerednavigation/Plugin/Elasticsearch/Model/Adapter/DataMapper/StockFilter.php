<?php
namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;

use Magento\Store\Model\ScopeInterface;


class StockFilter implements DataMapperInterface
{
    const FIELD_NAME = 'stock_status';
    const DOCUMENT_FIELD_NAME = 'quantity_and_stock_status';
    const INDEX_DOCUMENT = 'document';

    
    private $lproductCollectionFactory;
    private $inStockIds = [];
    private $stockStatusResource;
    private $scopeConfig;
    protected $lstockStatusResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $lproductCollectionFactory,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $lstockStatusResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->lproductCollectionFactory = $lproductCollectionFactory;
        $this->lstockStatusResource = $lstockStatusResource;
        $this->scopeConfig = $scopeConfig;
    }

    
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        $filterVal = isset($context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME])
            ? $context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME]
            : $this->getIsInStock($entityId, $storeId);
        return [self::FIELD_NAME => $filterVal];
    }

    
    private function getIsInStock($pentityId, $storeId)
    {
        return in_array($pentityId, $this->getInStockIds($storeId))
            ? 1 : 2;
    }

   
    private function getInStockIds($storeId)
    {
        if (!isset($this->inStockIds[$storeId])) {
            $collection = $this->lproductCollectionFactory->create()->addStoreFilter($storeId);
            $this->lstockStatusResource->addStockDataToCollection($collection, true);
            $this->inStockIds[$storeId] = $collection->getAllIds();
        }

        return $this->inStockIds[$storeId];
    }

    public function isAllowed()
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
