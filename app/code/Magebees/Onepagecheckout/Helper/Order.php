<?php
namespace Magebees\Onepagecheckout\Helper;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
class Order
{
    protected $orderFactory;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
	
    public function __construct(
        \Magebees\Onepagecheckout\Factory\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    public function getLastInsertedOrderId()
    {
        $orders = $this->getOrderCollection();
        if (empty($orders)) {
            return 0;
        }
        $orderItems = $orders->getItems();
        if (empty($orderItems)) {
            return 0;
        }
        $firstOrder = array_shift($orderItems);
        if (empty($firstOrder)) {
            return 0;
        }
        return (int)$firstOrder->getEntityId();
    }
    public function getEmptyOrder()
    {
        return $this->orderFactory->create();
    }
    public function getOrderById($orderId)
    {
        if (empty($orderId)) {
            return $this->getEmptyOrder();
        }
        try {
            return $this->orderRepository->get($orderId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return $this->getEmptyOrder();
        }
    }
    protected function getOrderCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('created_at', AbstractCollection::SORT_ORDER_DESC);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setPageSize(1);
        $searchCriteria->setCurrentPage(0);
        $searchCriteria->getSortOrders();
        $orders = $this->orderRepository->getList($searchCriteria);
        return $orders;
    }
}