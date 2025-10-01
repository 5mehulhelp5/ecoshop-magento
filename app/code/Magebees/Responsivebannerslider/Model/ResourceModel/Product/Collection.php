<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Product;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Product', 'Magebees\Responsivebannerslider\Model\ResourceModel\Product');
	}
	
}
