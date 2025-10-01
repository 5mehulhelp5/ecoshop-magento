<?php
namespace Magebees\Blog\Controller\Category;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
   	
	protected $post;	     
	protected $request;
	protected $design;
	protected $resultPageFactory;
	protected $_bloghelper;
	protected $_pageConfig;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design,
		\Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magento\Framework\View\Page\Config $pageConfig
		
	) {
       $this->request = $request;
	    parent::__construct($context);
        $this->post = $post;
		$this->design = $design;
		$this->resultPageFactory = $resultPageFactory;
		$this->_bloghelper = $bloghelper;
		$this->_pageConfig = $pageConfig;
	}
    public function execute()
    {	
		
		try {
			
			$data = $this->request->getParams();
			
			if(isset($data['category_id']))
			{
			$category_id = $data['category_id'];
			}	
			$this->_view->loadLayout();
			$breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
			$pathInfo = $this->request->getOriginalPathInfo();
			$data = $this->request->getParams();
			$breadcumInfoList = $this->_bloghelper->getCategoryBreadcumInfo($pathInfo);
			$lastbreadcum = end($breadcumInfoList);	
				
			foreach($breadcumInfoList as $key => $value):
			$breadcrumbs->addCrumb($key, array(
				'label'=>$value['title'],
				'title'=>$value['title'],
				'link'=>$value['url'],
			));
			endforeach;
			
			if(isset($lastbreadcum['title']))
			{
			$pageTitle = $lastbreadcum['title'];
			$this->_view->getLayout()->getBlock('page.main.title')->setPageTitle($pageTitle);
			$this->_pageConfig->getTitle()->set(__($pageTitle));
			}
			$page = $this->resultPageFactory->create(false, ['isIsolated' => true]); 
			//$design_layout = '2columns-left'; 
        	//$page->getConfig()->setPageLayout($design_layout);
			$this->_view->renderLayout();
			
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
        }
	}
}
