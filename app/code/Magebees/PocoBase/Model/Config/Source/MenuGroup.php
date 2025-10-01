<?php
namespace Magebees\PocoBase\Model\Config\Source;
class MenuGroup implements \Magento\Framework\Option\ArrayInterface
{	
	protected $_menucreatergroup;

	public function __construct(
		\Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatergroup
	) {
		$this->_menucreatergroup = $menucreatergroup;
	}
	
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
		return $this->_menucreatergroup->getCollection()->toOptionArray();
	}
}

