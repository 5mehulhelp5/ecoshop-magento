<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Slide;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Slide', 'Magebees\Responsivebannerslider\Model\ResourceModel\Slide');
	}
	
}
