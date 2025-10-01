<?php
namespace Magebees\PocoThemes\Model\ResourceModel\Product\Index\Viewed;
class Collection extends \Magento\Reports\Model\ResourceModel\Product\Index\Viewed\Collection
{

    protected $httpContext;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
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
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Framework\App\Http\Context  $httpContext,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
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
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $customerVisitor,
            $connection
        );

        $this->httpContext= $httpContext;
    }

    protected function _getWhereCondition()
    {
	  $condition = [];
      $myCustomerId = $this->httpContext->getValue('my_customer_id');
      $myVisitorId = $this->httpContext->getValue('my_visitor_id');
	 
      if($myCustomerId){
          $condition['customer_id'] = $myCustomerId;
      }elseif ($this->_customerSession->isLoggedIn()) {
          $condition['customer_id'] =    $this->_customerSession->getCustomerId();
          $this->httpContext->setValue('my_customer_id',$condition['customer_id'],false);
      }elseif ($this->_customerId) {
        $condition['customer_id'] = $this->_customerId;
        $this->httpContext->setValue('my_customer_id',$condition['customer_id'],false);
      }elseif($myVisitorId) {
         $condition['visitor_id'] = $myVisitorId;
      }else{
         $condition['visitor_id'] = $this->_customerVisitor->getId();
         if($condition['visitor_id'])
           $this->httpContext->setValue('my_visitor_id',$condition['visitor_id'],false);
        }
	return $condition;
   }
}

