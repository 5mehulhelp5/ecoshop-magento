<?php
namespace Magebees\Blog\Controller\Post;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action {
    
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
		\Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\DesignInterface $design, 
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $pageConfig) 
	{
        $this->request = $request;
        parent::__construct($context);
        $this->post = $post;
        $this->_bloghelper = $bloghelper;
        $this->design = $design;
        $this->resultPageFactory = $resultPageFactory;
        $this->_pageConfig = $pageConfig;
    }
    public function execute() {
        try {
            $this->_view->loadLayout();
            $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
            $pathInfo = $this->request->getOriginalPathInfo();
            $breadcumInfoList = $this->_bloghelper->getBreadcumInfo($pathInfo);
            $lastbreadcum = end($breadcumInfoList);
            foreach ($breadcumInfoList as $key => $value):
                $breadcrumbs->addCrumb($key, array('label' => $value['title'], 'title' => $value['title'], 'link' => $value['url'],));
            endforeach;
            if (isset($lastbreadcum['title'])) {
                $pageTitle = $lastbreadcum['title'];
                $this->_view->getLayout()->getBlock('page.main.title')->setPageTitle($pageTitle);
                $this->_pageConfig->getTitle()->set(__($pageTitle));
            }
            $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
            //$post_design_layout = '2columns-left'; 
			//$page->getConfig()->setPageLayout($post_design_layout);
            $this->_view->renderLayout();
        }
        catch(\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
