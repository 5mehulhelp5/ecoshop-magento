<?php
namespace  Magebees\Blog\Block\Adminhtml\Comment;
use Magento\Store\Model\Store;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{	
		protected $_productFactory;
		protected $_commentcollection;
		protected $moduleManager;
		protected $_type;
		protected $_setsFactory;
		protected $_status;
		protected $_visibility;
		protected $_websiteFactory;
		protected $resourceConnection;
		protected $_helper;
		protected $_systemStore;
		
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magebees\Blog\Model\ResourceModel\Comment\Collection $commentcollection,
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
        $this->_commentcollection = $commentcollection;
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
        $this->setId('commentGrid');
        $this->setDefaultSort('comment_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_commentcollection;
        


       
       $this->setCollection($collection);
       
       
        return parent::_prepareCollection();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('comment_id');
        $this->getMassactionBlock()->setFormFieldName('commentIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('*/*/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        
        
        $status= array('0'=>'Pending','1'=>'Approved','-1'=>'Not Approved');
		
		
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
        
        return $this;
    }

    protected function _prepareColumns()
    {
        
      
       $this->addColumn(
            'author_nickname',
            [
                'header' => __('Nick Name'),
                'align' => 'left',
                'index' => 'author_nickname'
            ]
        );
		$this->addColumn(
            'text',
            [
                'header' => __('Comment'),
                'align' => 'left',
                'index' => 'text'
            ]
        );
		$this->addColumn(
            'author_email',
            [
                'header' => __('Email'),
                'align' => 'left',
                'index' => 'author_email'
            ]
        );
		
		$this->addColumn(
            'post_id',
            [
                'header' => __('Post ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'post_id',
				'type' => 'options',
				 'options' => $this->_helper->getPostListOptionArray()
            ]
        );
		
		
		$this->addColumn(
            'author_type',
            [
                'header' => __('Author Type'),
                'align' => 'left',
				'width' => '50px',
				'type' => 'options',
                'index' => 'author_type',
				'options' => $this->_helper->getAuthorType()
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
            'creation_time',
            [
                'header' => __('Create Time'),
                'type' => 'datetime',
                'index' => 'creation_time'
				
            ]
        );
		
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'type' => 'options',
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Comment\Edit\Tab\Renderer\Status',
                'options' =>  [
                        1 => 'Approved',
                        0 => 'Pending',
						-1 => 'Not Approved'
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
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Comment\Edit\Tab\Renderer\IsImported',
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
                                'field' => 'comment_id'
                         ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true
            ]
        );
		$this->addColumn(
            'reply',
            [
                'header' => __('Reply'),
               'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' =>  [
                         [
                                'caption' =>__('Reply'),
                                'url' =>  [
                                        'base' => '*/*/edit'
                                ],
                                'field' => 'parent_comment_id'
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
        return $this->getUrl('*/*/edit', ['comment_id' => $row->getCommentId()]);
    }
	protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }
	    $this->getCollection()->addFieldToFilter('store_id', array('finset' => $value));
    }
	
}
