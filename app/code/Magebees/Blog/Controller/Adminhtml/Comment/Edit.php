<?php
namespace Magebees\Blog\Controller\Adminhtml\Comment;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $comment;
	public $registry;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magebees\Blog\Model\Comment $comment,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->comment = $comment;
		$this->registry = $registry;
	}
    public function execute()
    {
       $id = $this->getRequest()->getParam('comment_id');
        // 2. Initial checking
        if ($id) {
            $this->comment->load($id);
            if (!$this->comment->getCommentId()) {
                $this->messageManager->addError(__('Blog Comment Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $this->comment->setData($data);
        }
        $this->registry->register('comment', $this->comment);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::comment');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Comment'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
