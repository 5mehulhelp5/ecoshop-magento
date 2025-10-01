<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Store;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Store', 'Magebees\Responsivebannerslider\Model\ResourceModel\Store');
	}
	
}
