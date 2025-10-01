<?php
namespace Magebees\ParallaxImage\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class ParallaxImage extends \Magento\Catalog\Block\Product\AbstractProduct
{
    
    protected $_productCollectionFactory;
  
    
    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;
	protected $urlHelper;
        
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
        //$this->setTemplate('parallax-image.phtml');
    }
    
    public function _toHtml()
    {
		$this->setTemplate('parallax-image.phtml');
        return parent::_toHtml();
    }
    
    public function _getProductCollection()
    {
        $pageSize=(int)$this->getData('wd_no_of_products');
        $category_id = $this->getData('wd_categories');
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('visibility', 4);
        
        $collection->addCategoriesFilter(['eq' => $category_id]);
           
        $collection->setPageSize($pageSize);
        
        $this->_productCollection = $collection;
        
        return $this->_productCollection;
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    public function getUniqueKey()
    {
        $key = uniqid();
        return $key;
    }
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
}
