<?php
namespace Magebees\Blog\Controller\Adminhtml\Tag;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $tag;	     
	public $registry;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magebees\Blog\Model\Tag $tag,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->tag = $tag;
		$this->registry = $registry;
	}
    public function execute()
    {
       $id = $this->getRequest()->getParam('tag_id');
        // 2. Initial checking
        if ($id) {
            $this->tag->load($id);
            if (!$this->tag->getTagId()) {
                $this->messageManager->addError(__('Blog Tag Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $this->tag->setData($data);
        }
        $this->registry->register('tag', $this->tag);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::tag');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Tag'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
