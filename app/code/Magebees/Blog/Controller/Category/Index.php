<?php
namespace Magebees\Blog\Controller\Category;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $post;	     
	protected $design;
	protected $resultPageFactory;
	
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebees\Blog\Model\Post $post,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design
    ) {
        parent::__construct($context);
        $this->post = $post;
		$this->design = $design;
		        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
		try {
		$this->_view->loadLayout();
        $page = $this->resultPageFactory->create(false, ['isIsolated' => true]); 
		//$design_layout = '2columns-left'; 
        //$page->getConfig()->setPageLayout($design_layout);
		$this->_view->renderLayout();
		
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
        }
    }
		

}
