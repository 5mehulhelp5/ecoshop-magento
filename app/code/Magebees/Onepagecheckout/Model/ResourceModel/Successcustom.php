<?php
namespace Magebees\Onepagecheckout\Model\ResourceModel;
class Successcustom extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('cws_magebees_ocp_successcustom', 'entity_id');
	
	}	
}
