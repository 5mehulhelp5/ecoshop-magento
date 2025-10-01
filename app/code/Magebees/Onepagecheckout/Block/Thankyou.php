<?php
namespace Magebees\Onepagecheckout\Block;
use Magento\Framework\ObjectManagerInterface;

class Thankyou extends \Magento\Sales\Block\Order\Totals
{
    protected $checkoutSession;
    protected $customerSession;
    protected $_orderFactory;
    protected $imageHelper;
    protected $productRepository;
    protected $productFactory;
	protected $objectManager;
    
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
		ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
		$this->objectManager = $objectManager;
    }
    public function getOrder()
    {
        return  $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
    }
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }
	public function getRealOrderId()
	{
    	$lastorderId = $this->checkoutSession->getLastOrderId();
    	return $lastorderId;
	}
    public function getProductImage($sku) 
    {
        $product = $this->loadProduct($sku);
        if($product->getImage() != "no_selection" && $product->getImage() != ""){
            $imagePath = $this->imageHelper->init($product, 'small_image')
                ->setImageFile($product->getImage())
                ->resize(100)
                ->getUrl();
        }else{
            $imagePath =  $this->imageHelper->getDefaultPlaceholderUrl('image'); 
        }           
        return $imagePath;
    }
    public function loadProduct($sku) {
        return $this->productRepository->get($sku);
    }
    public function getBundleOptionsHtml($pid)
    {
        $_product = $this->objectManager->create('\Magento\Catalog\Model\Product')->load($pid);
    }
}