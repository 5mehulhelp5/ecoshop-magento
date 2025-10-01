<?php
namespace Magebees\Blog\Controller\Adminhtml\UrlRewrite;

class Delete extends \Magento\Backend\App\Action
{
	protected $helper;
	protected $urlrewrite;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magebees\Blog\Model\UrlRewrite $urlrewrite,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    	$this->urlrewrite = $urlrewrite;
		$this->helper = $helper;
    }
    public function execute()
    {
        $urlrewrite_id = $this->getRequest()->getParam('category_id');
        try {
                $urlrewrite = $this->urlrewrite->load($urlrewrite_id);
				$urlrewrite->delete();
                $this->messageManager->addSuccess(
                    __('URL was deleted successfully!')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
