<?php
namespace Magebees\Blog\Controller\Adminhtml\Urlrewrite;
class MassDelete extends \Magento\Backend\App\Action
{
	protected $helper;
	protected $blogurlrewrite;
     public function __construct(
        \Magento\Backend\App\Action\Context $context,
    	\Magebees\Blog\Model\UrlRewrite $blogurlrewrite,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    		$this->blogurlrewrite = $blogurlrewrite;
		$this->helper = $helper;

    }
 
    public function execute()
    {
        $urlIds = $this->getRequest()->getParam('urlIds');
        if (!is_array($urlIds) || empty($urlIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($urlIds);
                foreach ($urlIds as $urlId) {
                    $urlrewrite= $this->blogurlrewrite->load($urlId);
					$urlrewrite->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been deleted.', count($urlIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
