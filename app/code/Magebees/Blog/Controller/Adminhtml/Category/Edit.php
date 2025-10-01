<?php
namespace Magebees\Blog\Controller\Adminhtml\Category;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $category;
	public $registry;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magebees\Blog\Model\Category $category,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->category = $category;
		$this->registry = $registry;
	}
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        // 2. Initial checking
        if ($id) {
            $this->category->load($id);
            if (!$this->category->getId()) {
                $this->messageManager->addError(__('Blog Categories Information Not Available.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $this->category->setData($data);
        }
        $this->registry->register('category', $this->category);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::category');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Categories'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
