<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Finder\Block\Adminhtml\Product\Edit\Tab\Products;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;
    protected $_linkFactory;
    protected $_setsFactory;
    protected $_productFactory;
    protected $_type;
    protected $_status;
    protected $_visibility;
    protected $_mapvalueFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_mapvalueFactory = $mapvalueFactory;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_section');
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        $this->setDefaultFilter(['product_sku' => 1]);
    }
    
    public function getSelectedProducts()
    {
        $products = $this->getFinderProduct();
        $model = $this->_coreRegistry->registry('finder_value');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $product_val = [];
            $product_val = explode("|", $model->getData('product_id'));
            if (!empty($products)) {
                $products = array_merge($product_val, $products);
                return $products;
            } else {
                $products = $product_val;
                return $products;
            }
        } else {
            return $products;
        }
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'product_sku') {
            $productIds = $this->getSelectedProducts();
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
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect('*');
      
        $collection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id'
        );
        $collection->joinAttribute(
            'custom_name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute(
            'visibility',
            'catalog_product/visibility',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left');
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_sku',
            [
                    'type' => 'checkbox',
                    'field_name' => 'product_sku',
                    'values' => $this->getSelectedProducts(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
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
            'sku',
            [
                    'header' => __('SKU'),
                    'index' => 'sku',
                    'header_css_class' => 'col-sku',
                    'column_css_class' => 'col-sku'
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
        return $this->getUrl('*/*/producttab', ['_current' => true]);
    }
}
