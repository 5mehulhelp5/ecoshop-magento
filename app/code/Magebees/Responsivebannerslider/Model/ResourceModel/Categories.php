<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Categories extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_category', 'category_id');
	
	}
	
}
