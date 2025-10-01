<?php
namespace Magebees\PocoBase\Model\Config\Source;
class BannerSlider implements \Magento\Framework\Option\ArrayInterface
{	
	protected $_responsivebannerslider;
	public function __construct(
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider
	) {
		$this->_responsivebannerslider = $responsivebannerslider;
	}
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
		return $this->_responsivebannerslider->getCollection()->toOptionArray();
	}
}

