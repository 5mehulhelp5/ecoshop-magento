<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class SelectPost extends Extended
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
        $this->setId('post_section');
        $this->setDefaultSort('post_id');
        $this->setUseAjax(true);
		if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_post' => 1));
        }
		
    }
 
    public function getSelectedPost()
    {
		$posts = $this->getSelectPost();
        $post_id= $this->getRequest()->getParam('post_id');
		if (!isset($post_id)) {
		$post_id= 0;
		}
		$collection = $this->postFactory->load($post_id);
		$data = $collection->getRelatedPostIds();
		$postids = explode(',', (string)$data);
		
		$postIds = array();
		foreach($postids as $post) {
		$postIds[] = $post;
		}
		 if ($post_id) {
            if (!empty($posts)) {
                $posts = array_merge($postids, $posts);
                return $posts;
            } else {
                $posts = $postIds;
                return $posts;
            }
        } else {
            return $posts;
        }
		return $posts;
	}
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_post') {
            $postIds = $this->getSelectedPost();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('post_id', ['in' => $postIds]);
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('post_id', ['nin' => $postIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    
    protected function _prepareCollection()
	{
		$post_id= $this->getRequest()->getParam('post_id');
		$collection = $this->postFactory->getCollection();
		$collection->addFieldToFilter('post_id',array('neq' => $post_id));
		$this->setCollection($collection);
		return parent::_prepareCollection();

	}
 
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_post',
            [
                    'type' => 'checkbox',
                    'name' => 'in_post',
                    'values' => $this->getSelectedPost(),
                    'align' => 'center',
                    'index' => 'post_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
        );
                        
        $this->addColumn(
            'related_post_ids',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'post_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
       
        $this->addColumn(
            'related_post_title',
            [
                'header' => __('Title'),
                'align' => 'left',
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'related_post_identifier',
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
        return $this->getUrl('*/*/selectpostgrid', ['_current' => true]);
    }
	
}
