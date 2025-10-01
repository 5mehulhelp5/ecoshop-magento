<?php
namespace Magebees\Blog\Controller\Post;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
ini_set('display_errors', 1);
error_reporting(E_ALL);
class View extends \Magento\Framework\App\Action\Action
{
	 
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
        \Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\Registry $registry
    ) {
       $this->request = $request;
        parent::__construct($context);
        $this->post = $post;
		$this->_bloghelper = $bloghelper;
		$this->design = $design;
		$this->resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;
		$this->_registry = $registry;
    }
    public function execute()
    {
		try {
			$data = $this->request->getParams();
			
			$post =  $this->_registry->registry('current_blog_post');
			if($post->getPostId()):
			$this->_view->loadLayout();
				$breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
				$pathInfo = $this->request->getOriginalPathInfo();
				$data = $this->request->getParams();
				$breadcumInfoList = $this->_bloghelper->getCategoryBreadcumInfo($pathInfo);
				$pageTitle = $post->getTitle();
				$this->_view->getLayout()->getBlock('page.main.title')->setPageTitle($pageTitle);
				$this->_pageConfig->getTitle()->set(__($pageTitle));
			 $breadcumInfoList[$post->getIdentifier()] = array('title'=>$post->getTitle(),'url'=>null);
			foreach($breadcumInfoList as $key => $value):
				$breadcrumbs->addCrumb($key, array(
					'label'=>$value['title'],
					'title'=>$value['title'],
					'link'=>$value['url'],
				));
			endforeach;
			 $page = $this->resultPageFactory->create(false, ['isIsolated' => true]); 
			
			//$post_design_layout = '2columns-left'; 
			//$page->getConfig()->setPageLayout($post_design_layout);
			$this->_view->renderLayout();
			endif;
		 } catch (\Exception $e) {
			 print_r($e->getMessage());die;
			$this->messageManager->addError($e->getMessage());
        }
		
		
		

    }
		

}
