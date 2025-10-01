<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_product', 'product_id');
	
	}
	
}
