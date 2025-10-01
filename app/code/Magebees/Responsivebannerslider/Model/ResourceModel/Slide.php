<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel;
class Slide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('responsivebannerslider_slide', 'slide_id');
	
	}
	
}
