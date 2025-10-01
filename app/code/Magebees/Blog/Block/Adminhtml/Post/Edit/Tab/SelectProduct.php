<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class SelectProduct extends Extended
{
    protected $_coreRegistry = null;
    protected $_linkFactory;
    protected $_setsFactory;
    protected $_productFactory;
    protected $_type;
    protected $_status;
    protected $_visibility;
    protected $categoryFactory;
    
    protected $postFactory;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
		\Magebees\Blog\Model\Category $category,
		\Magebees\Blog\Model\Post $post,
        array $data = []
    ) {
		$this->postFactory= $post;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->categoryFactory= $category;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_section');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
		if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
		
    }
 
    public function getSelectedProduct()
    {
		$products = $this->getSelectPost();
        $post_id= $this->getRequest()->getParam('post_id');
		if (!isset($post_id)) {
		$post_id= 0;
		}
		$collection = $this->postFactory->load($post_id);
		$data = $collection->getProductsId();
		$productids = explode(',', (string)$data);
		
		$productIds = array();
		foreach($productids as $product) {
		$productIds[] = $product;
		}
		 if ($post_id) {
            if (!empty($products)) {
                $products = array_merge($productids, $products);
                return $products;
            } else {
                $products = $productIds;
                return $products;
            }
        } else {
            return $products;
        }
		return $products;
	}
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_product') {
            $productIds = $this->getSelectedProduct();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    
    protected function _prepareCollection()
	{
		$collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            '*'
        );
        $this->setCollection($collection);
		return parent::_prepareCollection();
		

	}
 
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                    'type' => 'checkbox',
                    'name' => 'in_product',
                    'values' => $this->getSelectedProduct(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
        );
		 $this->addColumn(
			'entity_id',
				[
				'header' => __('ID'),
				'sortable' => true,
				'index' => 'entity_id',
				'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
				]
		);					
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        
		$this->addColumn(

            'type',

            [

                        'header' => __('Type'),

                        'index' => 'type_id',

                        'type' => 'options',

                        'options' => $this->_type->getOptionArray(),

                        'header_css_class' => 'col-type',

                        'column_css_class' => 'col-type'

                ]

        );
		$sets = $this->_setsFactory->create()->setEntityTypeFilter(

            $this->_productFactory->create()->getResource()->getTypeId()

        )->load()->toOptionHash();

 

        $this->addColumn(

            'set_name',

            [

                        'header' => __('Attribute Set'),

                        'index' => 'attribute_set_id',

                        'type' => 'options',

                        'options' => $sets,

                        'header_css_class' => 'col-attr-name',

                        'column_css_class' => 'col-attr-name'

                ]

        );
		$this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );
		$this->addColumn(

            'visibility',

            [

                        'header' => __('Visibility'),

                        'index' => 'visibility',

                        'type' => 'options',

                        'options' => $this->_visibility->getOptionArray(),

                        'header_css_class' => 'col-visibility',

                        'column_css_class' => 'col-visibility'

                ]

        );
		$this->addColumn(

            'price',

            [

                        'header' => __('Price'),

                        'type' => 'currency',

                        'currency_code' => (string)$this->_scopeConfig->getValue(

                            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,

                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE

                        ),

                        'index' => 'price',

                        'header_css_class' => 'col-price',

                        'column_css_class' => 'col-price'

                ]

        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/selectproductgrid', ['_current' => true]);
    }
	
}
