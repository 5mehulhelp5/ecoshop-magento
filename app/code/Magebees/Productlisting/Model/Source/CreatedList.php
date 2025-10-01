<?php
namespace Magebees\Productlisting\Model\Source;


class CreatedList implements \Magento\Framework\Option\ArrayInterface
{
	protected $_listing;
    public function __construct(
        \Magebees\Productlisting\Model\Productlisting $listing
    ) {
        $this->_listing = $listing;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_listing->getCollection()->toOptionArray();
    }
}
