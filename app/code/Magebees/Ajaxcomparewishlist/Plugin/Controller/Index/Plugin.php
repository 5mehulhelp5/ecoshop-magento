<?php
namespace Magebees\Ajaxcomparewishlist\Plugin\Controller\Index;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Wishlist\Controller\AbstractIndex as WishlistAction;
use Magento\Wishlist\Controller\Index\Add;

class Plugin
{
    protected $productRepository;
    protected $jsonHelper;
    protected $helper;
    protected $customerSession;
    protected $messageManager;
    protected $pageFactory;
	protected $cacheManager;
	protected $mbSession;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        JsonHelper $jsonHelper,
		\Magebees\Ajaxcomparewishlist\Helper\Data $helper,
        CustomerSession $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Framework\Session\SessionManagerInterface $mbSession
    )
    {
        $this->productRepository = $productRepository;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->pageFactory = $pageFactory;
		$this->cacheManager = $cacheManager;
		$this->mbSession = $mbSession;
    }
    public function beforeDispatch(WishlistAction $subject, RequestInterface $request)
    {		
        if (!$this->helper->isAjaxWishEnabled()) {
            return;
        }
		$id = (int) $request->getParam('product');
		$cache_type = array();
		$cache_type[] = 'block_html';
		$this->cacheManager->clean($cache_type);
		$this->mbSession->start();
		$sessVal = $this->mbSession->getPid();
		if($sessVal != ""){
			$this->mbSession->unsPid();
		}
		$this->mbSession->setPid($id);
		
        if ($request->isAjax() && !$this->customerSession->isLoggedIn()) {
            $response = $subject->getResponse();
            if ($response->isRedirect()) {
                $response
                    ->clearHeader('Location')
                    ->setStatusCode(200);
            }
            $response->representJson(
                $this->jsonHelper->jsonEncode(
                    [
                        'success' => false,
                        'error'   => 'not_logged_in'
                    ]
                )
            );
        }
    }
    public function afterExecute(WishlistAction $subject, $result)
    {
        $request = $subject->getRequest();
        if (!$this->helper->isAjaxWishEnabled() || !$request->isAjax()) {
            return $result;
        }
        $response = $subject->getResponse();
        $page = $this->pageFactory->create();
        $page->addHandle('wishlist_index_index');
        $data = ['success' => true];
        if ($block = $this->getWishlistBlock()) {
            $data['wishlist'] = $block->toHtml();
        }
        if ($subject instanceof Add) {
            $id = (int) $request->getParam('product');
            $product = $this->productRepository->getById($id);
            $data['message'] = $page->getLayout()->createBlock('Magento\Catalog\Block\Product\AbstractProduct')
								->setData('product', $product)
								->setTemplate('Magebees_Ajaxcomparewishlist::wishpopup.phtml')
								->toHtml();
            
        }
        $this->messageManager->getMessages(true);
        return $response->representJson(
            $this->jsonHelper->jsonEncode($data)
        );
    }
    protected function getWishlistBlock()
    {
        $page = $this->pageFactory->create();
        $page->addHandle('wishlist_index_index');
        return $page->getLayout()->getBlock('customer.wishlist');
    }
}