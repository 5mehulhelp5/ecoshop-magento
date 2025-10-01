<?php
namespace Magebees\Onepagecheckout\Model\ResourceModel\Successcustom;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Onepagecheckout\Model\Successcustom', 'Magebees\Onepagecheckout\Model\ResourceModel\Successcustom');
	}	
}
