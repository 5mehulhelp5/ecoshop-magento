<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Pages extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_page', 'page_id');
	
	}
	
}
