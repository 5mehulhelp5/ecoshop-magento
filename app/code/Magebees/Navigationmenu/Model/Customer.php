<?php
namespace Magebees\Navigationmenu\Model;

class Customer extends \Magento\Framework\Model\AbstractModel
{
	protected $_session;
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\SessionFactory $session,
		array $data = []
    ) {
        $this->_session = $session;
		parent::_construct();
    }
    public function isLoggedIn()
    {
        if ($this->_session->create()->isLoggedIn()) {
            return 1;
        } else {
            return 0;
        }
    }
    public function getUserPermission($customerStatus=null,$customerGroupId=null)
    {
		
		$permission = [];
        $permission [] = -2; /* For Public Menu Items*/
        $customerGroup = null;
		if(($customerGroupId) && ($customerStatus))
		{
			if($customerStatus=='login')
			{
				$permission [] = -1;/* For Register User Menu Items*/
				$permission [] = $customerGroupId;
			}else{
				$permission [] =$customerGroupId;
			}
		}else{
				if ($this->_session->create()->isLoggedIn()) {
					$customerGroup =$this->_session->create()->getCustomerGroupId();
					$permission [] = -1;/* For Register User Menu Items*/
					$permission [] = $customerGroup;
				} else {
					$permission [] =$this->_session->create()->getCustomerGroupId();
				}
			
		}
		return $permission;
    }
}
