<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Pages;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Pages', 'Magebees\Responsivebannerslider\Model\ResourceModel\Pages');
	}
	
}
