<?php
namespace Magebees\Productlisting\Controller\Index;

class Result extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
	public $_registry;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		$this->_registry = $registry;
		return parent::__construct($context);
	}

	public function execute()
	{
		$listing_id = $this->getRequest()->getParam('listing_id');
		$current_prodlist_list = $this->_registry->registry('current_prodlist_list');
		
		$this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__($current_prodlist_list->getTitle()));
		$this->_view->renderLayout();
	}
}