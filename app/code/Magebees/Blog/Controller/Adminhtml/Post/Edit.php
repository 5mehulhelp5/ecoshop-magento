<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $post;
	public $registry;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magebees\Blog\Model\Post $post,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->post = $post;
		$this->registry = $registry;
	}
    public function execute()
    {
        $id = $this->getRequest()->getParam('post_id');
        // 2. Initial checking
        if ($id) {
            $this->post->load($id);
            if (!$this->post->getPostId()) {
                $this->messageManager->addError(__('Blog Post Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $this->post->setData($data);
        }
        $this->registry->register('post', $this->post);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::post');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Post'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
