<?php
namespace Magebees\Blog\Controller\Adminhtml\Urlrewrite;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $urlrewrite;
	public $registry;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magebees\Blog\Model\UrlRewrite $urlrewrite,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->urlrewrite = $urlrewrite;
		$this->registry = $registry;
	}
    public function execute()
    {
        $id = $this->getRequest()->getParam('url_id');
        // 2. Initial checking
        if ($id) {
            $this->urlrewrite->load($id);
            if (!$this->urlrewrite->getId()) {
                $this->messageManager->addError(__('Blog Url Rewrite Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $this->urlrewrite->setData($data);
        }
        $this->registry->register('urlrewrite', $this->urlrewrite);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::urlrewrite');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Url Rewrite'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
