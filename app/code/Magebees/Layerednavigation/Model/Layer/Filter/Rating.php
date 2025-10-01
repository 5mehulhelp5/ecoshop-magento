<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Framework\Registry;

class Rating extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    
     const NOT_RATED_LABEL = 'Not Yet Rated';
      const RATING_COLLECTION_FLAG = 'rating_filter_applied';
    protected $blockFactory;
     private $attributeCode = 'rating_summary';
      protected $_stockResource;
    
    protected $stars = [
        1 => 20,
        2 => 40,
        3 => 60,
        4 => 80,
        5 => 100,
        6 => -1
    ];

     protected $productMetadata;
    protected $_escaper;
    protected $itemCollectionProvider;
    protected $objectManager;
    protected $_productModel;
    protected $productStatus;

    protected $productVisibility;
    protected $resourceConnection;
    protected $_scopeConfig;
    protected $_requestVar;
    protected $layerHelper;



    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $itemCollectionProvider,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
         \Magebees\Layerednavigation\Helper\Data $layerHelper,
                array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->productMetadata = $productMetadata;
        $this->_escaper = $escaper;
        $this->itemCollectionProvider = $itemCollectionProvider;
        $this->objectManager = $objectManager;
        $this->_productModel = $productModel;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->resourceConnection = $resourceConnection;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_requestVar = $this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->httpRequest = $httpRequest;
        $this->blockFactory = $blockFactory;
        $this->layerHelper = $layerHelper;
        $this->_stockResource = $stockResource;
    }
    
  
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
           
        $IsElasticSearchEnabled=$this->layerHelper->IsElasticSearch();     
        $filter =$request->getParam($this->getRequestVar(), null);
        if (is_null($filter)) {
            return $this;
        }
         $condition = $this->stars[$filter];

        if ($filter == 6) {
            $condition = new \Zend_Db_Expr("IS NULL");
        }
        $collection = $this->getLayer()->getProductCollection();
         $collection->setFlag(self::RATING_COLLECTION_FLAG, $filter);
        if($IsElasticSearchEnabled)
        {
          $this->getLayer()->getProductCollection()->addFieldToFilter('rating_summary', $condition);
        }
        else
        {
             $select = $collection->getSelect();

        $minRating = (array_key_exists($filter, $this->stars))
            ? $this->stars[$filter]
            : 0;
        $rat_table=$this->resourceConnection->getTableName('rating_option_vote_aggregated');
        $select->joinLeft(
            ['rating' =>$rat_table],
            sprintf(
                '`rating`.`entity_pk_value`=`e`.entity_id                   
                    AND `rating`.`store_id`  =  %d',
                $this->storeManager->getStore()->getId()
            ),
            ''
        );
        if ($minRating == "-1") {
            $select->where('`rating`.`percent` IS NULL');
        } else {
            $select->where(
                '`rating`.`percent` >= ?',
                $minRating
            );
        }

        }       

        $state = $this->_createItem($this->getLabelHtml($filter), $filter);
        $this->getLayer()->getState()->addFilter($state);
        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        $rating_filter_config=$this->_scopeConfig->getValue('layerednavigation/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $label=$rating_filter_config['label'];
        return __($label);
    }
       /**
        * Get data array for building attribute filter items
        *
        * @throws \Magento\Framework\Exception\LocalizedException
        * @return array
        */
    protected function _getItemsData()
    {
		$data = array();
        $currentEngine=$this->layerHelper->getCurrentSearchEngine();
        $IsElasticSearchEnabled=$this->layerHelper->IsElasticSearch();
        if($IsElasticSearchEnabled)
        {

         $optionsFacetedData = $this->getLayer()->getProductCollection()->getFacetedData($this->attributeCode);               
        $currentValue = $this->httpRequest->getQuery($this->_requestVar);

        for ($i = 5; $i >= 1; $i--) {
            $r_count=0;           

            foreach($optionsFacetedData as $k=>$v )
            {
                if($k>=$this->stars[$i])
                {
                    $r_count+=$v['count'];              
                }

            }

         
        if($r_count)
        {
                    $data[] = [
                    'label' => $this->getLabelHtml($i),
                    'value' => ($currentValue == $i) ? null : $i,
                    'count' => $r_count,
                    'real_count' => $r_count,
                    'option_id' => $i,
                    ];
        }
        }
           /* $data[] = [
                'label' => $this->getLabelHtml(6),
                'value' => ($currentValue == 6) ? null : 6,
                'count' => $r_count,
                'real_count' => $r_count,
                'option_id' => 6,
            ];*/
        }
        else
        {
           $count = $this->_getCount();
            $currentValue = $this->httpRequest->getQuery($this->_requestVar);

        for ($i = 5; $i >= 1; $i--) {
            $data[] = [
            'label' => $this->getLabelHtml($i),
            'value' => ($currentValue == $i) ? null : $i,
            'count' => $count[($i - 1)],
            'real_count' => ((isset($count[$i]) && $i != 5 ? $count[$i] : 0) - $count[($i - 1)]),
            'option_id' => $i,
            ];
        }
            $data[] = [
                'label' => $this->getLabelHtml(6),
                'value' => ($currentValue == 6) ? null : 6,
                'count' => $count[5],
                'real_count' => $count[5],
                'option_id' => 6,
            ];  
        }
        return $data;
    }
    public function _getCount()
    {
        
        $collection = $this->getLayer()->getProductCollection();
        $connection = $collection->getConnection();
        $connection
          ->query('SET @ONE :=0, @TWO := 0, @THREE := 0, @FOUR := 0, @FIVE := 0, @NOT_RATED := 0');
        $select = clone $collection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $where = $select->getPart(\Magento\Framework\DB\Select::WHERE);
         $from = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (!isset($from['stock_status_index'])) {

                $select->join(
                    [
                        'stock_status_index' => $this->_stockResource->getMainTable()
                    ],
                    'e.entity_id = stock_status_index.product_id',
                    []
                );
             }
        foreach ($where as $key => $part) {
            if (strpos($part, "percent") !== false) {
                if ($key == 0) {
                    $where[$key] = "1";
                } else {
                    unset($where[$key]);
                }
            }
        }
        $rat_table=$this->resourceConnection->getTableName('rating_option_vote_aggregated');
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $where);
        $select->joinLeft(
            ['rsc' => $rat_table],
            sprintf(
                '`rsc`.`entity_pk_value`=`e`.entity_id
               
                AND `rsc`.store_id  =  %d',
                $this->storeManager->getStore()->getId()
            ),
            ['e.entity_id','rsc.percent']
        );
        if (version_compare($this->productMetadata->getVersion(), '2.1.0', '<')) {
            $select2 = new \Magento\Framework\DB\Select($connection);
        } else { // for version 2.1.0 & up
        
            $select2 = new \Magento\Framework\DB\Select($connection, $this->objectManager->get('\Magento\Framework\DB\Select\SelectRenderer'));
        }
       

        $select2->from($select);
        $select = $select2;

        $columns = new \Zend_Db_Expr("
            IF(`t`.`percent` >= 20, @ONE := @ONE + 1, 0),
            IF(`t`.`percent` >= 40, @TWO := @TWO + 1, 0),
            IF(`t`.`percent` >= 60, @THREE := @THREE + 1, 0),
            IF(`t`.`percent` >= 80, @FOUR := @FOUR + 1, 0),
            IF(`t`.`percent` >= 100, @FIVE := @FIVE + 1, 0),
            IF(`t`.`percent` IS NULL, @NOT_RATED := @NOT_RATED + 1, 0)
        ");
        $select->columns($columns);
        $connection->query($select);
        $result = $connection->fetchRow('SELECT @ONE, @TWO, @THREE, @FOUR, @FIVE, @NOT_RATED;');
        return array_values($result);
    }
    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = [];
        
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setOptionId($itemData['option_id']);
            $item->setRealCount($itemData['real_count']);
            if ($itemData['count']) {
                $items[] = $item;
            }
        }
        $this->_items = $items;
        return $this;
    }
    protected function getLabelHtml($countStars)
    {
        if ($countStars == 6) {
            return __('Not Yet Rated');
        }
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->blockFactory->createBlock('\Magento\Framework\View\Element\Template');
        $block->setTemplate('Magebees_Layerednavigation::layer/rating.phtml');
        if ($this->getLayer()->getProductCollection()->getFlag(self::RATING_COLLECTION_FLAG)) {
            $block->setData('filterval', $this->getLayer()->getProductCollection()->getFlag(self::RATING_COLLECTION_FLAG));
        }
        
        $block->setData('star', $countStars);
        $html = $block->toHtml();
        return $html;
    }
}
