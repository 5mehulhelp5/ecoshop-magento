<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class SelectCategory extends Extended
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
        $this->setId('category_section');
        $this->setDefaultSort('category_id');
        $this->setUseAjax(true);
		if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_category' => 1));
        }
		
    }
 
    public function getSelectedCategory()
    {
		$categorys = $this->getSelectCategory();
        $post_id= $this->getRequest()->getParam('post_id');
		if (!isset($post_id)) {
		$post_id= 0;
		}
		$collection = $this->postFactory->load($post_id);
		$data = $collection->getCategoryIds();
		$categoryids = explode(',', (string)$data);
		
		$categoryIds = array();
		foreach($categoryids as $category) {
		$categoryIds[] = $category;
		}
		 if ($post_id) {
            if (!empty($categorys)) {
                $categorys = array_merge($categoryIds, $categorys);
                return $categorys;
            } else {
                $categorys = $categoryIds;
                return $categorys;
            }
        } else {
            return $categorys;
        }
		return $categorys;
	}
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_category') {
            $categoryIds = $this->getSelectedCategory();
            if (empty($categoryIds)) {
                $categoryIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('category_id', ['in' => $categoryIds]);
            } else {
                if ($categoryIds) {
                    $this->getCollection()->addFieldToFilter('category_id', ['nin' => $categoryIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    
    protected function _prepareCollection()
	{
	$collection = $this->categoryFactory->getCollection();
	$this->setCollection($collection);
	return parent::_prepareCollection();
	}
 
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_category',
            [
                    'type' => 'checkbox',
                    'name' => 'in_category',
                    'values' => $this->getSelectedCategory(),
                    'align' => 'center',
                    'index' => 'category_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
        );
                        
        $this->addColumn(
            'category_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'category_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'categorytitle',
            [
                'header' => __('Title'),
                'align' => 'left',
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'categoryidentifier',
            [
                'header' => __('URL Key'),
                'align' => 'left',
                'index' => 'identifier'
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/selectcategorygrid', ['_current' => true]);
    }
	
}
