<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	public $registry;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magento\Framework\Registry $registry
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_coreSession = $coreSession;
		$this->registry = $registry;
	}
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Import'));
		
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
