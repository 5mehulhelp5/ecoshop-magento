<?php
namespace Magebees\Onepagecheckout\Block\Checkout;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Helper\Address as AddressHelper;
use Magebees\Onepagecheckout\Helper\Configurations as OneStepConfig;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;

class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{
    protected $_oneStepConfig;
    protected $_regionCollection;
    protected $_directoryHelper;

    public function __construct(
        AddressHelper $addressHelper,
        Session $customerSession,
        CustomerRepository $customerRepository,
        DirectoryHelper $directoryHelper,
        OneStepConfig $oneStepConfig,
        RegionCollection $regionCollection
    )
    {
        $this->_oneStepConfig = $oneStepConfig;
        $this->_regionCollection = $regionCollection;
        $this->_directoryHelper = $directoryHelper;
        parent::__construct($addressHelper, $customerSession, $customerRepository, $directoryHelper);
    }

    protected function getDefaultValue($attributeCode): ?string
    {
        if ($this->_oneStepConfig->getFullRequest() == 'checkout_index_index') 
		{	
            switch ($attributeCode) {
                case 'firstname':
                    if ($this->getCustomer()) {
                        return $this->getCustomer()->getFirstname();
                    }
                    break;
                case 'lastname':
                    if ($this->getCustomer()) {
                        return $this->getCustomer()->getLastname();
                    }
                    break;
                case 'country_id':
					if ($this->_oneStepConfig->getDefaultCountry()) {
						return $this->_oneStepConfig->getDefaultCountry();
					} else {
						return $this->_directoryHelper->getDefaultCountry();
					}
                case 'region_id':
					if ($this->_oneStepConfig->getDefaultRegionId() && $this->_oneStepConfig->getDefaultRegionId() !='null')
					{
						return $this->_oneStepConfig->getDefaultRegionId();
					} else {
						return 0;
					}
                case 'postcode':
					return $this->_oneStepConfig->getDefaultPostalCode();
                case 'city':
					return $this->_oneStepConfig->getDefaultCity();
            }
            return null;
        } else {
            return parent::getDefaultValue($attributeCode);
        }
    }
}