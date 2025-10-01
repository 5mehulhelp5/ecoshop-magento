<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Categories;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Categories', 'Magebees\Responsivebannerslider\Model\ResourceModel\Categories');
	}
	
}
