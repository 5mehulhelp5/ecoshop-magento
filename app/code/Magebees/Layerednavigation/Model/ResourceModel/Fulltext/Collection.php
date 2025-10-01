<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\ResourceModel\Fulltext;

use Magento\Framework\DB\Select;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Adapter\Mysql\Adapter;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Response\Aggregation\Value;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Framework\Exception\LocalizedException;
/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    
 
    protected $queryResponse;
    protected $queryFactory = null;
	private $search;
  	private $relevanceOrderDirection = null;
 	private $searchResult;
 	private $lsearchResultFactory;    
    private $requestBuilder;
    public $_memRequestBuilder;
    public $_andLogicCollection;
 	private $layerfilterBuilder;
    private $searchEngine;
    private $queryText;
    private $order = null;
    private $searchRequestName;
  	private $searchLayerCriteriaBuilder;
    private $temporaryStorageFactory;
    protected $stars=[1 => 20,2 => 40,3 => 60,4 => 80,5 => 100,6 => -1];
    protected $resourceConnection;
    protected $helper;
    protected $request;
    protected $objectManager;
    protected $scopeConfig;
    protected $_sessionManager;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $dataEntityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $leavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $lcatalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $lproductOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\Session $sessionManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $dbConnection = null,
        $searchRequestName = 'catalog_view_container',
		?\Magento\Framework\Api\Search\SearchResultFactory $lsearchResultFactory = null,
        ?ProductLimitationFactory $lproductLimitationFactory = null,
        ?MetadataPool $lmetadataPool = null
    ) {
        $this->queryFactory = $catalogSearchData;
        $this->resourceConnection = $resourceConnection;
        $this->_sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
		if ($lsearchResultFactory === null) {
            $this->lsearchResultFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Api\Search\SearchResultFactory::class);
        }
        parent::__construct(
            $dataEntityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $leavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $lcatalogProductFlatState,
            $scopeConfig,
            $lproductOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $dbConnection,
			$lproductLimitationFactory,
            $lmetadataPool
        );


        $this->helper = $helper;
        $this->request = $request;        
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchRequestName = $searchRequestName;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $filtercondition = null)
    {
		$currentEngine=$this->helper->getCurrentSearchEngine();
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
		if($IsElasticSearchEnabled)	{
			$this->getLayerSearchCriteriaBuilder();
			$this->getLayerFilterBuilder();
			if (!is_array($filtercondition) || !in_array(key($filtercondition), ['from', 'to'], true)) {
				$this->layerfilterBuilder->setField($field);
				$this->layerfilterBuilder->setValue($filtercondition);
				$this->searchLayerCriteriaBuilder->addFilter($this->layerfilterBuilder->create());
			} else {
				if (!empty($filtercondition['from'])) {
					$this->layerfilterBuilder->setField("{$field}.from");
					$this->layerfilterBuilder->setValue($filtercondition['from']);
					$this->searchLayerCriteriaBuilder->addFilter($this->layerfilterBuilder->create());
				}
				if (!empty($filtercondition['to'])) {
					$this->layerfilterBuilder->setField("{$field}.to");
					$this->layerfilterBuilder->setValue($filtercondition['to']);
					$this->searchLayerCriteriaBuilder->addFilter($this->layerfilterBuilder->create());
				}
			}
		}else{
			 if ($this->queryResponse !== null) {
            throw new \RuntimeException('Illegal state');
        }
        if (!is_array($filtercondition) || (!in_array(key($filtercondition), ['from', 'to'], true) && $field != 'visibility')) {
            $this->requestBuilder->bind($field, $filtercondition);
        } else {
            if (!empty($filtercondition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $filtercondition['from']);
            }
            if (!empty($filtercondition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $filtercondition['to']);
            }
        }
      
		}
        return $this;
       
       
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);
        return $this;
    }
    
    public function getProdcutIdsArr($element)
    {
         $currentEngine=$this->helper->getCurrentSearchEngine();
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
        if($IsElasticSearchEnabled)
        {
            return $element['product_id'];
        }
        else
        {
            return $element['sku'];
        }
        
    }
    
    //=== YMM ==/
    /**
     * @inheritdoc
     */
	 private function getLayerSearchCriteriaBuilder()
    {
        if ($this->searchLayerCriteriaBuilder === null) {
            $this->searchLayerCriteriaBuilder = ObjectManager::getInstance()
                ->get(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        }
        return $this->searchLayerCriteriaBuilder;
    }
	 private function getLayerFilterBuilder()
    {
        if ($this->layerfilterBuilder === null) {
            $this->layerfilterBuilder = ObjectManager::getInstance()->get(\Magento\Framework\Api\FilterBuilder::class);
        }
        return $this->layerfilterBuilder;
    }

	 private function getSearch()
    {
        if ($this->search === null) {
            $this->search = ObjectManager::getInstance()->get(\Magento\Search\Api\SearchInterface::class);
        }
        return $this->search;
    }
	 public function setLayerSearchCriteriaBuilder(\Magento\Framework\Api\Search\SearchCriteriaBuilder $object)
    {
        $this->searchLayerCriteriaBuilder = $object;
    }
	 public function setLayerFilterBuilder(\Magento\Framework\Api\FilterBuilder $object)
    {
        $this->layerfilterBuilder = $object;
    }
	 public function setSearch(\Magento\Search\Api\SearchInterface $object)
    {
        $this->search = $object;
    }
    protected function _renderFiltersBefore()
    {        
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
        $path = $this->request->getRequestString();
      
        //====YMM ===/
       
        $search = strpos($path, 'finder/');
        //var_dump($path);die;

    
		
		if($IsElasticSearchEnabled){
			$this->getLayerSearchCriteriaBuilder();
			$this->getLayerFilterBuilder();
			$this->getSearch();

			if ($this->queryText) {
				$this->layerfilterBuilder->setField('search_term');
				$this->layerfilterBuilder->setValue($this->queryText);
				$this->searchLayerCriteriaBuilder->addFilter($this->layerfilterBuilder->create());
			}

			$lpriceRangeCalculation = $this->_scopeConfig->getValue(
				\Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
			if ($lpriceRangeCalculation) {
				$this->layerfilterBuilder->setField('price_dynamic_algorithm');
				$this->layerfilterBuilder->setValue($lpriceRangeCalculation);
				$this->searchLayerCriteriaBuilder->addFilter($this->layerfilterBuilder->create());
			}

			$lsearchCriteria = $this->searchLayerCriteriaBuilder->create();
			$lsearchCriteria->setRequestName($this->searchRequestName);
			try {
				$this->searchResult = $this->getSearch()->search($lsearchCriteria);
			} catch (EmptyRequestDataException $e) {
				/** @var \Magento\Framework\Api\Search\SearchResultInterface $searchResult */
				$this->searchResult = $this->lsearchResultFactory->create()->setItems([]);
			} catch (NonExistingRequestNameException $e) {
				$this->_logger->error($e->getMessage());
				throw new LocalizedException(__('An error occurred. For details, see the error log.'));
			}

			$ltemporaryStorage = $this->temporaryStorageFactory->create();
			$table = $ltemporaryStorage->storeApiDocuments($this->searchResult->getItems());

			$this->getSelect()->joinInner(
				[
					'search_result' => $table->getName(),
				],
				'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
				[]
			);

			if ($this->relevanceOrderDirection) {
				$this->getSelect()->order(
					'search_result.'. TemporaryStorage::FIELD_SCORE . ' ' . $this->relevanceOrderDirection
				);
			}
        
		}else{
			if ($search) {
				$skus=array_values($skus);
				$this->requestBuilder->bind('sku', $skus);
			}
			$sku_arr=[];      
      
        	$this->requestBuilder->bindDimension('scope', $this->getStoreId());
			if ($this->queryText) {
				$this->requestBuilder->bind('search_term', $this->queryText);
			}
			$dbConnection=$this->resourceConnection->getConnection();
			$select=$this->getSelect();
			$result=$dbConnection->query($select)->fetchAll();
			foreach ($result as $res) {
				$sku_arr[]=$res['sku'];
			}

			if(!empty($sku_arr)){
			 $this->requestBuilder->bind('and_logic_term', $sku_arr);
			}
			$lpriceRangeCalculation = $this->_scopeConfig->getValue(
				\Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
			if ($lpriceRangeCalculation) {
				$this->requestBuilder->bind('price_dynamic_algorithm', $lpriceRangeCalculation);
			}

			$this->requestBuilder->setRequestName($this->searchRequestName);
			$queryRequest = $this->requestBuilder->create();
			$this->queryResponse = $this->searchEngine->search($queryRequest);

			$ltemporaryStorage = $this->temporaryStorageFactory->create();
			$table = $ltemporaryStorage->storeDocuments($this->queryResponse->getIterator());

			$select->joinInner(
				[
					'search_result' => $table->getName(),
				],
				'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
				[]
			);

			$result=$dbConnection->query($select)->fetchAll();
			$this->_totalRecords =count($result);

			if ($this->order && 'relevance' === $this->order['field']) {
				$this->getSelect()->order('search_result.'. TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
			}       
		}
		return parent::_renderFiltersBefore();
    }
   
  
    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
		if($IsElasticSearchEnabled)	{
			if ($attribute === 'relevance') {
            $this->relevanceOrderDirection = $dir;
			} else {
				parent::setOrder($attribute, $dir);
			}
		}else{
			$this->order = ['field' => $attribute, 'dir' => $dir];
			if ($attribute != 'relevance') {
				parent::setOrder($attribute, $dir);
			}
		}        
        return $this;
    }

    /**
     * Stub method for compatibility with other search engines
     *
     * @return $this
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param string $field
     * @return array
     * @throws StateException
     */
    public function getFacetedData($field)
    {
		
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
		if($IsElasticSearchEnabled)	{ 
			$this->_renderFilters();
			$result = [];
			$aggregations = $this->searchResult->getAggregations();
			// This behavior is for case with empty object when we got EmptyRequestDataException
			if (null !== $aggregations) {
				$bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
				if ($bucket) {
					foreach ($bucket->getValues() as $value) {
						$metrics = $value->getMetrics();
						$result[$metrics['value']] = $metrics;
					}
				} else {
					throw new StateException(__("The bucket doesn't exist."));
				}
			}
       
		}else{
			$this->_renderFilters();
			$result = [];
			$aggregations = $this->queryResponse->getAggregations();
			$bucket = $aggregations->getBucket($field . '_bucket');
			if ($bucket) {
				foreach ($bucket->getValues() as $value) {
					$metrics = $value->getMetrics();
					$result[$metrics['value']] = $metrics;
				}
			} else {
			   // throw new StateException(__('Bucket do not exists'));
				return false;
			}        
		}
		return $result;
    }

    /**
     * Specify category filter for product collection
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return $this
     */
    public function addCategoryFilter(\Magento\Catalog\Model\Category $category)
    {
        $this->addFieldToFilter('category_ids', $category->getId());
        return parent::addCategoryFilter($category);
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->addFieldToFilter('visibility', $visibility);
        return parent::setVisibility($visibility);
    }
	protected function _isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
