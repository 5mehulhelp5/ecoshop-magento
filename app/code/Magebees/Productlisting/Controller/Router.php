<?php
namespace Magebees\Productlisting\Controller;

/**
 * Productlisting Custom router Controller Router
 *
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;
    protected $resultFactory;
    protected $_scopeConfig;
    protected $_objectManager;
    protected $_storeManager;
    protected $_registry;
    protected $_productlistingfactory;
    protected $_productlistingcollection;
 
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    
	public function __construct(
		\Magento\Framework\App\ActionFactory $actionFactory,
		\Magento\Framework\Controller\ResultFactory $resultFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager, 
		\Magento\Framework\Registry $registry, 
		\Magebees\Productlisting\Model\ProductlistingFactory $productlistingfactory,
		\Magebees\Productlisting\Model\ResourceModel\Productlisting\Collection $productlistingcollection
		) 
	{
        $this->actionFactory = $actionFactory;
        $this->resultFactory = $resultFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->_productlistingfactory = $productlistingfactory;
		$this->_productlistingcollection = $productlistingcollection;
    }
    
    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
		$identifier = trim($request->getPathInfo(), '/');
		$pageId = trim($request->getPathInfo(), '/');
		$pathInfo = explode('/', (string)$identifier);
		 $pathInfo = array_combine(range(1, count($pathInfo)), $pathInfo);
		 
		 if(isset($pathInfo[1]) && ($pathInfo[1]=='prodlist')){
			if(isset($pathInfo[2])){
			 $page_url = explode(".", (string)$pathInfo[2]);
			 
			 if(isset($page_url[0])){
				$product_list_url = $page_url[0]; 
				$productlisting = $this->getProductListingByUrlkey($product_list_url);
				if($productlisting->getFirstItem()->getListingId()):
                   $listing_id = $productlisting->getFirstItem()->getListingId();
        			return $this->forawardToProductList($request, $listing_id, $pageId);
				endif;
			 }
			}
		 }
		 
		 
		 return null;
		  
		
    }
	public function getProductListingByUrlkey($url_key) {
		
        $storeId = $this->_storeManager->getStore()->getId();
        $productlisting = $this->_productlistingfactory->Create()->getCollection();
        $productlisting->addFieldToFilter('status', array('eq' => 1));
        $productlisting->addFieldToFilter(['stores', 'stores'], [['finset' => 0], ['finset' => $storeId]]);
		$productlisting->addFieldToFilter('slider_options', array('regexp' => '\\b' . $url_key . '\\b'));
		return $productlisting;
	}
	public function forawardToProductList($request, $listing_id, $pageId) {
		$productlisting = $this->_productlistingfactory->Create()->load($listing_id);
        if ($productlisting->getListingId()):
            $this->_registry->register('current_prodlist_list', $productlisting);
        endif;
		$request->setModuleName('prodlist')->setControllerName('index')->setActionName('result')->setParam('listing_id', $listing_id);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('prodlist')->setControllerName('index')->setActionName('result')->setParam('listing_id', $listing_id));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
	
}
