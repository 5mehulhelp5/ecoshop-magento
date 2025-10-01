<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $aclRetriever;
    protected $authSession;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        parent::__construct($context);
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }
    
    public function execute()
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if ($role->getRoleName()=="Todaydeal") {
            $this->messageManager->addNotice(__('This is demo store so you are not allowed to update details.'));
            $this->_redirect('*/*/index');
            return '0';
        }
        
        $todaydealIds = $this->getRequest()->getParam('todaydeal');
        
        if (!is_array($todaydealIds) || empty($todaydealIds)) {
            $this->messageManager->addError(__('Please select deal(s).'));
        } else {
            try {
                foreach ($todaydealIds as $todaydealId) {
                    $model = $this->_objectManager->get('Magebees\TodayDealProducts\Model\Deal')->load($todaydealId);
                    $model->delete();
                }
                        
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been deleted.', count($todaydealIds))
                    );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('todaydealpro/dealproducts/index');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todaydealpro_content');
    }
}
