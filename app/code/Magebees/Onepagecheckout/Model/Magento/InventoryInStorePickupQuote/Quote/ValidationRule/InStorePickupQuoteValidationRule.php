<?php
namespace Magebees\Onepagecheckout\Model\Magento\InventoryInStorePickupQuote\Quote\ValidationRule;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\InventoryInStorePickupApi\Model\GetPickupLocationInterface;
use Magento\InventoryInStorePickupQuote\Model\GetWebsiteCodeByStoreId;
use Magento\InventoryInStorePickupQuote\Model\IsPickupLocationShippingAddress;
use Magento\InventoryInStorePickupShippingApi\Model\IsInStorePickupDeliveryCartInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface;

class InStorePickupQuoteValidationRule extends \Magento\InventoryInStorePickupQuote\Model\Quote\ValidationRule\InStorePickupQuoteValidationRule
{

    private $validationResultFactory;
    private $isPickupLocationShippingAddress;
    private $getPickupLocation;
    private $getWebsiteCodeByStoreId;
    private $isInStorePickupDeliveryCart;

    public function __construct(
        ValidationResultFactory $validationResultFactory,
        IsPickupLocationShippingAddress $isPickupLocationShippingAddress,
        GetPickupLocationInterface $getPickupLocation,
        GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId,
        IsInStorePickupDeliveryCartInterface $isInStorePickupDeliveryCart
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->isPickupLocationShippingAddress = $isPickupLocationShippingAddress;
        $this->getPickupLocation = $getPickupLocation;
        $this->getWebsiteCodeByStoreId = $getWebsiteCodeByStoreId;
        $this->isInStorePickupDeliveryCart = $isInStorePickupDeliveryCart;
    }

    public function validate(Quote $quote): array
    {
        $validationErrors = [];

        if (!$this->isInStorePickupDeliveryCart->execute($quote)) {
            return [$this->validationResultFactory->create(['errors' => $validationErrors])];
        }

        $address = $quote->getShippingAddress();
		$pickupLocation = $this->getPickupLocation($quote, $address);
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quoteadd = $resource->getTableName('quote_address');

		$con1 = "quote_id = ".$quote->getId();
		$con2 = "address_type = 'shipping'";
		
		$que = "SELECT * FROM " .$quoteadd. " WHERE " .$con1." AND ".$con2;
		$row = $connection->fetchRow($que);
		$shippingAddressId = $row['address_id'];
		
		$checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
		$pickupLocationCode = $checkoutSession->getData('pickupLocationCode');
		
		if(isset($pickupLocationCode))
		{
			$connection = $resource->getConnection();
			$iplqa = $resource->getTableName('inventory_pickup_location_quote_address');	
			$con = "address_id = ".$shippingAddressId;
			$que = "SELECT * FROM " . $iplqa . " WHERE " . $con;
			$row = $connection->fetchRow($que);
			
			if(empty($row)){
				$data = [
					'address_id' => $shippingAddressId,
					'pickup_location_code' => (string)$pickupLocationCode,
				];
				$connection->insert($iplqa, $data);
			}
			
			$sessionObj = $objectManager->create('\Magento\Framework\Session\SessionManagerInterface');
			$sessionObj->start();
			$sessionObj->setShippingAddressId($shippingAddressId);
			$sessionObj->setPickupLocationCode($pickupLocationCode);
		}
        if (!$pickupLocation && !$pickupLocationCode) {
            $validationErrors[] = __('Quote does not have Pickup Location assigned.');
        }
		
        if ($pickupLocation && !$pickupLocationCode && !$this->isPickupLocationShippingAddress->execute($pickupLocation, $address)) {
            $validationErrors[] = __('Pickup Location Address does not match Shipping Address for In-Store Pickup Quote.');
        }
        return [$this->validationResultFactory->create(['errors' => $validationErrors])];
    }

    /**
     * Get Pickup Location entity, assigned to Shipping Address.
     *
     * @param CartInterface $quote
     * @param AddressInterface $address
     *
     * @return PickupLocationInterface|null
     * @throws NoSuchEntityException
     */
    private function getPickupLocation(CartInterface $quote, AddressInterface $address): ?PickupLocationInterface
    {
        if (!$address->getExtensionAttributes() || !$address->getExtensionAttributes()->getPickupLocationCode()) {
            return null;
        }

        return $this->getPickupLocation->execute(
            $address->getExtensionAttributes()->getPickupLocationCode(),
            SalesChannelInterface::TYPE_WEBSITE,
            $this->getWebsiteCodeByStoreId->execute((int)$quote->getStoreId())
        );
    }
}

	