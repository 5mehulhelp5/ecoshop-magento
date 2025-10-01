<?php
namespace  Magebees\Blog\Block\Adminhtml\Post;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_productFactory;
    protected $_postcollection;
    protected $moduleManager;
    protected $_type;
    protected $_setsFactory;
    protected $_status;
    protected $_visibility;
    protected $_websiteFactory;
    protected $_helper;
    protected $_systemStore;
	protected $resourceConnection;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magebees\Blog\Model\ResourceModel\Post\Collection $postcollection,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magebees\Blog\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        
        $this->_productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
        $this->_type = $type;
        $this->_postcollection = $postcollection;
        $this->_setsFactory = $setsFactory;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_websiteFactory = $websiteFactory;
        $this->_helper = $helper;
        $this->resourceConnection = $resourceConnection;
		$this->_systemStore = $systemStore;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('post_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_postcollection;
        


       
       $this->setCollection($collection);
       
       
        return parent::_prepareCollection();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('post_id');
        $this->getMassactionBlock()->setFormFieldName('postIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('*/*/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        
        
        $status=$this->_helper->getEnableDisableOptionArray();
		 
        $this->getMassactionBlock()->addItem(
            'status',
            [
                        'label' => __('Change Status'),
                        'url' =>$this->getUrl('*/*/massstatus', ['_current'=>true]),
                        'additional' => [
                        'visibility' => [
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Status'),
                         'values' => $status
                                ]
                            ]
                ]
        );
		$yes_no_options=$this->_helper->getyesnoOptionArray();

		
		$this->getMassactionBlock()->addItem(
            'draft',
            [
                        'label' => __('Change Draft'),
                        'url' =>$this->getUrl('*/*/massaction', ['_current'=>true]),
                        'additional' => [
                        'visibility' => [
                         'name' => 'draft',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Save As Draft'),
                         'values' => $yes_no_options
                                ]
                            ]
                ]
        );
		$this->getMassactionBlock()->addItem(
            'is_featured',
            [
                        'label' => __('Change Is Featured'),
                        'url' =>$this->getUrl('*/*/massaction', ['_current'=>true]),
                        'additional' => [
                        'visibility' => [
                         'name' => 'is_featured',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Change Is Featured'),
                         'values' => $yes_no_options
                                ]
                            ]
                ]
        );
		$this->getMassactionBlock()->addItem(
            'include_in_recent',
            [
                        'label' => __('Include In Recent Post'),
                        'url' =>$this->getUrl('*/*/massaction', ['_current'=>true]),
                        'additional' => [
                        'visibility' => [
                         'name' => 'include_in_recent',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Include In Recent Post'),
                         'values' => $yes_no_options
                                ]
                            ]
                ]
        );
		return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'post_id',
            [
                'header' => __('ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'post_id'
            ]
        );
        
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'align' => 'left',
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'identifier',
            [
                'header' => __('URL Key'),
                'align' => 'left',
                'index' => 'identifier'
            ]
        );
		$this->addColumn(
            'category_ids',
            [
                'header' => __('Category'),
                'align' => 'left',
				'type' => 'options',
                'index' => 'category_ids',
				'renderer'=>  'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category',
				'filter_condition_callback' => [$this, '_filterCategoryCondition'],
				 'options' =>$this->_helper->getCategoryListOptionArray()
            ]
        );
		$this->addColumn(
            'update_time',
            [
                'header' => __('Update Time'),
                'align' => 'left',
                'type' => 'datetime',
				'index' => 'update_time'
				 
            ]
        );
		$this->addColumn(
            'publish_time',
            [
                'header' => __('Published'),
                'type' => 'datetime',
                'index' => 'publish_time'
				
            ]
        );
		
		 if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Store View'),
                    'sortable' => false,
                    'index' => 'store_id',
					'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'renderer'=>  'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Store',
					'filter_condition_callback' => [$this, '_filterStoreCondition'],
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }
		
        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'is_active',
                'type' => 'options',
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\IsActive',
                'options' =>  [
                        1 => 'Enabled',
                        2 => 'Disabled'
                ]
            ]
        );
	$this->addColumn(
            'is_imported',
            [
                'header' => __('Imported'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'is_imported',
                'type' => 'options',
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\IsImported',
                'options' =>  [
                        1 => 'Imported',
                        0 => 'Created'
                ]
            ]
        );	
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
               'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' =>  [
                         [
                                'caption' =>__('Edit'),
                                'url' =>  [
                                        'base' => '*/*/edit'
                                ],
                                'field' => 'post_id'
                         ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true
            ]
        );
        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['post_id' => $row->getPostId()]);
    }
	protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }
	    $this->getCollection()->addFieldToFilter('store_id', array('finset' => $value));
    }
	protected function _filterCategoryCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }
	    $this->getCollection()->addFieldToFilter('category_ids', array('finset' => $value));
    }
	
}
