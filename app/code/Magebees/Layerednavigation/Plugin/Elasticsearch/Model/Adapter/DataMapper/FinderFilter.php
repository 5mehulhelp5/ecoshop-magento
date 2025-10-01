<?php
namespace Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Magebees\Layerednavigation\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;

use Magento\Store\Model\ScopeInterface;


class FinderFilter implements DataMapperInterface
{
    const FIELD_NAME = 'finder';
    const DOCUMENT_FIELD_NAME = 'finder';
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
            : $entityId;
            
        return [self::FIELD_NAME => $filterVal];
    }

    public function isAllowed()
    {
       
            return true;
          
      
    }
}
