<?php

namespace Magebees\Blog\Controller\Adminhtml\Tag;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Blog::tag');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Tags Management'));
		return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
