<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateAddress implements ObserverInterface
{
    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();	
		$sessionObj = $objectManager->create('\Magento\Framework\Session\SessionManagerInterface');
		$sessionObj->start();
		$pickupLocationCode = $sessionObj->getPickupLocationCode();
		$shippingAddressId = $sessionObj->getShippingAddressId();
			
		if(isset($pickupLocationCode) && isset($shippingAddressId)){		
			$resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();			
			$inventory_source = $resource->getTableName('inventory_source');
			$condition = "source_code = '".$pickupLocationCode."'";
			$query = "SELECT * FROM " . $inventory_source . " WHERE " . $condition;
			$result = $connection->fetchRow($query);
			
			$quote_address = $resource->getTableName('sales_order_address');
			$sql = "UPDATE ".$quote_address." SET 
				firstname = '".$result['frontend_name']."', 
				lastname = 'Store', 
				street = '".$result['street']."', 
				city = '".$result['city']."', 
				country_id = '".$result['country_id']."', 
				region = '".$result['region']."', 
				region_id = ".$result['region_id'].", 
				postcode = ".$result['postcode'].", 
				telephone = ".$result['phone']." 
				WHERE quote_address_id = ".$shippingAddressId." AND address_type = 'shipping'";
						
			$connection->query($sql);
			
			$sessionObj->unsPickupLocationCode();
			$sessionObj->unsShippingAddressId();
		}
        //$this->orderRepository->save($order);
    }
}