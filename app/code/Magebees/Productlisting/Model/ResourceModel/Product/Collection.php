<?php
namespace Magebees\Productlisting\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const SELECT_COUNT_SQL_TYPE_CART = 1;
    protected $_prodEntityId;
    protected $_productEntityTableName;
    protected $_productEntityAttributeSetId;
    protected $_selectCountSqlType = 0;
    protected $_eventTypeFactory;
    protected $_productType;
    protected $_config;
  
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $dataEntityFactory,
        \Psr\Log\LoggerInterface $psrLogger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $dbFetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        $connection = null
    ) {
        $this->setProductEntityId($product->getEntityIdField());
        $this->setProductEntityTableName($product->getEntityTable());
        $this->setProductAttributeSetId($product->getEntityType()->getDefaultAttributeSetId());
        parent::__construct(
            $dataEntityFactory,
            $psrLogger,
            $dbFetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $optionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->_productType = $catalogProductType;
        $this->_config = $scopeConfig->getValue('bestseller/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function setSelectCountSqlType($sqlType)
    {
        $this->_selectCountSqlType = $sqlType;
        return $this;
    }

    public function setProductEntityId($productEntityId)
    {
        $this->_prodEntityId = (int)$productEntityId;
        return $this;
    }

    public function getProductEntityId()
    {
        return $this->_prodEntityId;
    }

    public function setProductEntityTableName($value)
    {
        $this->_productEntityTableName = $value;
        return $this;
    }

    public function getProductEntityTableName()
    {
        return $this->_productEntityTableName;
    }

    public function getProductAttributeSetId()
    {
        return $this->_productEntityAttributeSetId;
    }

    public function setProductAttributeSetId($attrValue)
    {
        $this->_productEntityAttributeSetId = $attrValue;
        return $this;
    }
 
    protected function _joinFields()
    {
        $this->_totals = new \Magento\Framework\Object();

        $this->addAttributeToSelect('entity_id')->addAttributeToSelect('name')->addAttributeToSelect('price');

        return $this;
    }

    public function getSelectCountSql()
    {
        if ($this->_selectCountSqlType == self::SELECT_COUNT_SQL_TYPE_CART) {
            $countSelect = clone $this->getSelect();
            $countSelect->reset()->from(
                ['quote_item_table' => $this->getTable('quote_item')],
                ['COUNT(DISTINCT quote_item_table.product_id)']
            )->join(
                ['quote_table' => $this->getTable('quote')],
                'quote_table.entity_id = quote_item_table.quote_id AND quote_table.is_active = 1',
                []
            );
            return $countSelect;
        }

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $countSelect->reset(\Magento\Framework\DB\Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");

        return $countSelect;
    }

    public function addOrdersCount($countFrom = '', $countTo = '')
    {
        $orderItemTableName = $this->getTable('sales_order_item');
        $productFieldName = sprintf('e.%s', $this->getProductEntityId());

        $this->getSelect()->joinLeft(
            ['order_items' => $orderItemTableName],
            "order_items.product_id = {$productFieldName}",
            []
        )->columns(
            ['orders' => 'COUNT(order_items2.item_id)']
        )->group(
            $productFieldName
        );

        $dateFilter = ['order_items2.item_id = order_items.item_id'];
        if ($countFrom != '' && $countTo != '') {
            $dateFilter[] = $this->_prepareBetweenSql('order_items2.created_at', $countFrom, $countTo);
        }

        $this->getSelect()->joinLeft(
            ['order_items2' => $orderItemTableName],
            implode(' AND ', $dateFilter),
            []
        );

        return $this;
    }

    public function addOrderedQty($countFrom = '', $countTo = '')
    {
        $adapter = $this->getConnection();
        
        $orderTableAliasName = $adapter->quoteIdentifier('order');

        $orderJoinCondition = [
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];

        $productJoinCondition = [
           'e.entity_id = order_items.product_id',
           $adapter->quoteInto('e.attribute_set_id = ?', $this->getProductAttributeSetId()),
        ];

        if ($countFrom != '' && $countTo != '') {
            $fieldName = $orderTableAliasName . '.created_at';

            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $countFrom, $countTo);
        }

        $this->getSelect()->reset()->from(
            ['order_items' => $this->getTable('sales_order_item')],
            ['ordered_qty' => 'SUM(order_items.qty_ordered)', 'order_items_name' => 'order_items.name']
        )->joinInner(
            ['order' => $this->getTable('sales_order')],
            implode(' AND ', $orderJoinCondition),
            []
        )->joinLeft(
            ['e' => $this->getProductEntityTableName()],
            implode(' AND ', $productJoinCondition),
            [
                'entity_id' => 'order_items.product_id',
                'attribute_set_id' => 'e.attribute_set_id',
                'type_id' => 'e.type_id',
                'sku' => 'e.sku',
                'has_options' => 'e.has_options',
                'required_options' => 'e.required_options',
                'created_at' => 'e.created_at',
                'updated_at' => 'e.updated_at'
            ]
        )
        ->group(
            'order_items.product_id'
        )->having(
            'SUM(order_items.qty_ordered) > ?',
            0
        );
        return $this;
    }

    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, ['carts', 'orders', 'ordered_qty'])) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    public function addViewsCount($countFrom = '', $countTo = '')
    {
        $eventTypeData = $this->_eventTypeFactory->create()->getCollection();
        foreach ($eventTypeData as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEventId = (int)$eventType->getId();
                break;
            }
        }
        $this->getSelect()->reset()->from(
            ['report_table_views' => $this->getTable('report_event')],
            ['views' => 'COUNT(report_table_views.event_id)']
        )->join(
            ['e' => $this->getProductEntityTableName()],
            $this->getConnection()->quoteInto(
                'e.entity_id = report_table_views.object_id AND e.attribute_set_id = ?',
                $this->getProductAttributeSetId()
            )
        )->where(
            'report_table_views.event_type_id = ?',
            $productViewEventId
        )->group(
            'e.entity_id'
        )->order(
            'views ' . self::SORT_ORDER_DESC
        )->having(
            'COUNT(report_table_views.event_id) > ?',
            0
        );

        if ($countFrom != '' && $countTo != '') {
            $this->getSelect()->where('logged_at >= ?', $countFrom)->where('logged_at <= ?', $countTo);
        }
        return $this;
    }

    protected function _prepareBetweenSql($fieldName, $countFrom, $countTo)
    {
        return sprintf(
            '(%s BETWEEN %s AND %s)',
            $fieldName,
            $this->getConnection()->quote($countFrom),
            $this->getConnection()->quote($countTo)
        );
    }

    public function addStoreRestrictions($storeIdArr, $websiteIds)
    {
        if (!is_array($storeIdArr)) {
            $storeIdArr = [$storeIdArr];
        }
        if (!is_array($websiteIds)) {
            $websiteIds = [$websiteIds];
        }

        $filters = $this->_productLimitationFilters;
        if (isset($filters['store_id'])) {
            if (!in_array($filters['store_id'], $storeIdArr)) {
                $this->addStoreFilter($filters['store_id']);
            } else {
                $this->addStoreFilter($this->getStoreId());
            }
        } else {
            $this->addWebsiteFilter($websiteIds);
        }

        return $this;
    }
}
