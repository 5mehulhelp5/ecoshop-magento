<?php
namespace Magebees\Blog\Controller\Author;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
	public $_registry;
	protected $post;	     
	protected $request;
	protected $design;
	protected $resultPageFactory;
	protected $_bloghelper;
	protected $_pageConfig;
	protected $_user;
	protected $configuration;
	
   	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\User\Model\User $user,
		\Magento\Framework\Registry $registry
		
	) {
       $this->request = $request;
	    parent::__construct($context);
        $this->_registry = $registry;
		$this->design = $design;
		$this->resultPageFactory = $resultPageFactory;
		$this->_bloghelper = $bloghelper;
		$this->_pageConfig = $pageConfig;
		$this->_user = $user;
		$this->configuration = $Configuration;
	}
    public function execute()
    {
		try {
		$this->_view->loadLayout();
		$current_author = $this->_registry->registry('current_blog_author');
		
		if($current_author->getUserId()):
		
		$breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
		$pathInfo = $this->request->getOriginalPathInfo();
		$breadcumInfoList = $this->_bloghelper->getBreadcumInfo($pathInfo);
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
		
		$pageTitle = $this->configuration->getConfig('blog/blogpage/title');
		$meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
		$meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
		$meta_robots = $this->configuration->getConfig('blog/author/robots');
		
		$page = $this->resultPageFactory->create(false, ['isIsolated' => true]); 
	
		$page->getConfig()->getTitle()->set($pageTitle); //setting the page
		$page->getConfig()->setDescription($meta_description); // set meta description
		$page->getConfig()->setKeywords($meta_keywords);// meta keywords
		$page->getConfig()->setRobots($meta_robots);// meta robots
			
		endif;
		
		//$design_layout = '2columns-left'; 
        //$page->getConfig()->setPageLayout($design_layout);
		$this->_view->renderLayout();
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
        
		}
    }
		

}
