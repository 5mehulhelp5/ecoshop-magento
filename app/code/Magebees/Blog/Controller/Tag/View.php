<?php
namespace Magebees\Blog\Controller\Tag;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

 use Magento\Framework\View\Result\PageFactory;
class View extends \Magento\Framework\App\Action\Action {
	public $_registry;
	protected $request;
	protected $post;
	protected $design;
	protected $resultPageFactory;
	protected $_bloghelper;
	protected $_pageConfig;
	protected $configuration;
    public function __construct(
		\Magento\Framework\App\Action\Context $context, 
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Framework\View\DesignInterface $design, 
		\Magebees\Blog\Model\Post $post, 
		\Magebees\Blog\Helper\Data $bloghelper, 
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\Registry $registry ) 
	{
        $this->request = $request;
        parent::__construct($context);
        $this->post = $post;
        $this->design = $design;
        $this->resultPageFactory = $resultPageFactory;
        $this->_bloghelper = $bloghelper;
        $this->_pageConfig = $pageConfig;
		$this->_registry = $registry;
		$this->configuration = $Configuration;
		
    }
    public function execute() {
        $this->_view->loadLayout();
        $data = $this->request->getParams();
        if (isset($data['tag_identifier'])) {
            $tag_identifier = $data['tag_identifier'];
        }
        $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
        $pathInfo = $this->request->getOriginalPathInfo();
        $data = $this->request->getParams();
        $breadcumInfoList = $this->_bloghelper->getBreadcumInfo($pathInfo);
        $lastbreadcum = end($breadcumInfoList);
        foreach ($breadcumInfoList as $key => $value):
            $breadcrumbs->addCrumb($key, array('label' => $value['title'], 'title' => $value['title'], 'link' => $value['url'],));
        endforeach;
        
		$current_blog_tag = $this->_registry->registry('current_blog_tag');
		$current_blog_tag->getMetaTitle();
		
		
		if($current_blog_tag->getTagId()):
			$meta_title = $current_blog_tag->getMetaTitle(); 
			$meta_keywords = $current_blog_tag->getMetaKeywords(); 
        	$meta_description = $current_blog_tag->getMetaDescription();
        	$meta_robots = $current_blog_tag->getMetaRobots();
		endif;
		
		if(!$meta_title)
		{
			$meta_title = $this->configuration->getConfig('blog/blogpage/title');
		}
		if(!$meta_keywords)
		{
			$meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
		}
		if(!$meta_description)
		{
			$meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
		}
		if(!$meta_robots)
		{
			$meta_robots = $this->configuration->getConfig('blog/blogpage/robots');
		}
		$pageMainTitle = $this->_view->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
			$pageTitle = $lastbreadcum['title'];
            $pageMainTitle->setPageTitle(__($pageTitle)); // Page <H1> Main title
        }
		
		$page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
		$page->getConfig()->getTitle()->set($meta_title); //setting the page
		$page->getConfig()->setDescription($meta_description); // set meta description
		$page->getConfig()->setKeywords($meta_keywords);// meta keywords
		$page->getConfig()->setRobots($meta_robots);// meta robots
		//$design_layout = '2columns-left';
        //$page->getConfig()->setPageLayout($design_layout);
        $this->_view->renderLayout();
    }
}
