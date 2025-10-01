<?php
namespace Magebees\Onepagecheckout\Factory;
class OrderFactory
{
	protected $objectManager;
	
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    public function create()
    {
        return $this->objectManager->create(\Magento\Sales\Api\Data\OrderInterface::class);
    }
}