<?php
namespace Magebees\Ajaxcomparewishlist\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
	protected $cacheManager;
	protected $_wishlistFactory;
	protected $_wishlistResource;
	protected $mbSession;
	protected $mbProduct;
	protected $messageManager;

	public function __construct(
        \Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
		\Magento\Wishlist\Model\ResourceModel\Wishlist $wishlistResource,
		\Magento\Framework\Session\SessionManagerInterface $mbSession,
		\Magento\Catalog\Model\Product $mbProduct,
		\Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->cacheManager = $cacheManager;
		$this->_wishlistFactory  = $wishlistFactory;
		$this->_wishlistResource = $wishlistResource;
		$this->mbSession = $mbSession;
		$this->mbProduct = $mbProduct;
		$this->messageManager = $messageManager;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$cache_type = array();
		$cache_type[] = 'block_html';
		$this->cacheManager->clean($cache_type);
        $customer = $observer->getEvent()->getCustomer();
		$customerId = $customer->getId();
		$this->mbSession->start();
		$chklogin = $this->mbSession->getChkLogin();
		$getPid = $this->mbSession->getPid();
		$product = $this->mbProduct->load($getPid);
		
		if(isset($chklogin) && isset($getPid))
		{
			$wishlist = $this->_wishlistFactory->create()->loadByCustomerId($customerId, true);
			$wishlist->addNewItem($product);
			$this->_wishlistResource->save($wishlist);
			$this->messageManager->addSuccess(__("Your Product is added to the Wishlist"));
			$this->mbSession->unsChkLogin();
			$this->mbSession->unsPid();		
		}
        return;
    }
}