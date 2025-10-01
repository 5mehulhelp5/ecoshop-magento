<?php
namespace Magebees\Blog\Controller\Index;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
   	protected $resultRedirect;
	public $_registry;
	protected $request;
	protected $configuration;
	protected $design;
	protected $resultPageFactory;
	protected $_bloghelper;
	protected $_pageConfig;
	protected $_urlInterface;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebees\Blog\Helper\Data $bloghelper,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\Controller\ResultFactory $result,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magento\Framework\Registry $registry
    ) {
		$this->request = $request;
        parent::__construct($context);
		$this->resultRedirect = $result;
        $this->_bloghelper = $bloghelper;
		$this->configuration = $Configuration;
		$this->design = $design;
		$this->resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;
		$this->_urlInterface = $urlInterface;
		$this->_registry = $registry;
    }
    public function execute()
    {
		 if (!$this->configuration->isEnableBlogModule()):
			$current_url = $this->_urlInterface->getUrl('cms/noroute/index');;   
			$resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($current_url);
            return $resultRedirect;
		 endif;
		$this->_registry->register('current_blog_home', true);
		$this->_view->loadLayout();
		
		
		
		
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
		}else{
		$pageTitle = $this->configuration->getConfig('blog/blogpage/title');
		}
		$meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
        $meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
        $meta_robots = $this->configuration->getConfig('blog/blogpage/robots');
        
		$page = $this->resultPageFactory->create(false, ['isIsolated' => true]); 
		$page->getConfig()->getTitle()->set($pageTitle); //setting the page
		$page->getConfig()->setDescription($meta_description); // set meta description
		$page->getConfig()->setKeywords($meta_keywords);// meta keywords
		$page->getConfig()->setRobots($meta_robots);// meta robots
		//$post_design_layout = '2columns-left'; 
       	//$page->getConfig()->setPageLayout($post_design_layout);
		$this->_view->renderLayout();
    }
		

}
