<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Store extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_store', 'store_ids');
	
	}
	
}
