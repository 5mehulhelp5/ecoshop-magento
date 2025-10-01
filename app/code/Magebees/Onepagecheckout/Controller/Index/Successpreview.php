<?php
namespace Magebees\Onepagecheckout\Controller\Index;
class Successpreview extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $registry;
	protected $checkoutSession;
	protected $_configHelper;
	protected $orderHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magebees\Onepagecheckout\Helper\Order $orderHelper,
		\Magebees\Onepagecheckout\Helper\Configurations $configHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->orderHelper = $orderHelper;
		$this->_configHelper = $configHelper;
    }
   
    public function execute()
    {
        $order = $this->getOrder();
        if (!$order->getEntityId()) {
            throw new \Magebees\Onepagecheckout\Exception\InvalidOrderId('Invalid order ID');
        }
        $this->registerOrder($order);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('successpreview_index_index');
        return $resultPage;
    }

    protected function getOrder()
    {
        $orderIdFromConfig = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_successpage/magebeesorder_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderIdFromConfig);
        if ($order->getEntityId()) {
            return $order;
        }
        $lastOrderId = $this->orderHelper->getLastInsertedOrderId();
        $order = $this->orderHelper->getOrderById($lastOrderId);
        if ($order->getEntityId()) {
            return $order;
        }
        return $this->orderHelper->getEmptyOrder();
    }
    protected function registerOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $currentOrder = $this->registry->registry('current_order');
        if (empty($currentOrder)) {
            $this->registry->register('current_order', $order);
        }
        $this->checkoutSession->setLastOrderId($order->getEntityId())->setLastRealOrderId($order->getIncrementId());
    }

}