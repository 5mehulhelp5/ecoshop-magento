<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class SelectTag extends Extended
{
    protected $_coreRegistry = null;
    protected $_linkFactory;
    protected $_setsFactory;
    protected $_productFactory;
    protected $_type;
    protected $_status;
    protected $_visibility;
    protected $_tagcollection;
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
		\Magebees\Blog\Model\ResourceModel\Tag\Collection $tagcollection,
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
         $this->_tagcollection = $tagcollection;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tag_section');
        $this->setDefaultSort('tag_id');
        $this->setUseAjax(true);
		if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_tags' => 1));
        }
		
    }
 
    public function getSelectedTags()
    {
		$tags = $this->getSelectTags();
        $post_id= $this->getRequest()->getParam('post_id');
		if (!isset($post_id)) {
		$post_id= 0;
		}
		$collection = $this->postFactory->load($post_id);
		$data = $collection->getTagIds();
		$tagids = explode(',', (string)$data);
		
		$tagIds = array();
		foreach($tagids as $tag) {
		$tagIds[] = $tag;
		}
		 if ($post_id) {
            if (!empty($tags)) {
                $tags = array_merge($tagIds, $tags);
                return $tags;
            } else {
                $tags = $tagIds;
                return $tags;
            }
        } else {
            return $tags;
        }
		return $tags;
	}
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_tags') {
            $tagIds = $this->getSelectedTags();
            if (empty($tagIds)) {
                $tagIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('tag_id', ['in' => $tagIds]);
            } else {
                if ($tagIds) {
                    $this->getCollection()->addFieldToFilter('tag_id', ['nin' => $tagIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    
    protected function _prepareCollection()
    {
        $collection = $this->_tagcollection;
        $this->setCollection($collection);
		return parent::_prepareCollection();
        
    }
 
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_tags',
            [
                    'type' => 'checkbox',
                    'name' => 'in_tags',
                    'values' => $this->getSelectedTags(),
                    'align' => 'center',
                    'index' => 'tag_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
        );
                        
        $this->addColumn(
            'tag_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'tag_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'tagtitle',
            [
                'header' => __('Tag Title'),
                'align' => 'left',
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'tagidentifier',
            [
                'header' => __('URL Key'),
                'align' => 'left',
                'index' => 'identifier'
            ]
        );
		
        $this->addColumn(
            'tagis_active',
            [
                'header' => __('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'is_active',
                'type' => 'options',
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Tag\Edit\Tab\Renderer\IsActive',
                'options' =>  [
                        1 => 'Enabled',
                        2 => 'Disabled'
                ]
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/selecttaggrid', ['_current' => true]);
    }
}
