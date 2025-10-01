<?php
namespace Magebees\Blog\Block\Post;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
class PostRelatedProducts extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface {
    
    protected $_itemCollection;
    
    protected $_catalogProductVisibility;
	protected $_productCollectionFactory;
	protected $_moduleManager;
	protected $_post;
    protected $configuration;
	public $_registry;
	
	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\Registry $registry,
		array $data = []) {
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_moduleManager = $moduleManager;
        $this->_post = $post;
        $this->configuration = $Configuration;
		$this->_registry = $registry;
        parent::__construct($context, $data);
    }
    public function getPost($post_id) {
        return $this->_post->load($post_id);
    }
    /**
     * Premare block data
     * @return $this
     */
    protected function _prepareCollection() {
		return $this;
        // $post = $this->getPost();
		$post =  $this->_registry->registry('current_blog_post');
		if($post->getPostId()):
        $this->_itemCollection = $post->getRelatedProducts()->addAttributeToSelect('required_options');
        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $this->_itemCollection->setPageSize((int)$this->_scopeConfig->getValue(\Magefan\Blog\Model\Config::XML_RELATED_PRODUCTS_NUMBER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->_itemCollection->getSelect()->order('rl.position', 'ASC');
        $this->_itemCollection->load();
        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
        return $this;
		endif;
    }
    /**
     * Retrieve true if Display Related Products enabled
     * @return boolean
     */
    public function displayProducts() {
        return true;
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getItems() {
        if (is_null($this->_itemCollection)) {
            $this->_prepareCollection();
        }
        return $this->_itemCollection;
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities() {
        $identities = []; 
		
		if($this->getItems()){
		foreach ($this->getItems() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }	
		}
        
        return $identities;
    }
    public function getRelatedProducts($product_ids) {
        $size = $this->postRelatedProductsSize();
        $productcollection = $this->_productCollectionFactory->create();
        $productcollection->addAttributeToSelect('*');
        $productcollection->addAttributeToSelect('required_options');
        $productcollection->addFieldToFilter('entity_id', ['in' => $product_ids]);
        $productcollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        if ($size):
            $productcollection->setPageSize($size);
        endif;
        $productcollection->load();
        return $productcollection;
    }
    public function enablePostRelatedProducts() {
        return $this->configuration->getConfig('blog/post_view/related_products/enable');
    }
    public function postRelatedProductsSize() {
        return $this->configuration->getConfig('blog/post_view/related_products/count');
    }
}
