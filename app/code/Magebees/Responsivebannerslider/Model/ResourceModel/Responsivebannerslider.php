<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Responsivebannerslider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_group', 'slidergroup_id');
	
	}
	
}
