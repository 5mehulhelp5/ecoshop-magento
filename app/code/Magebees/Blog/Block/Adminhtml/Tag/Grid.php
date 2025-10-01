<?php
namespace  Magebees\Blog\Block\Adminhtml\Tag;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_productFactory;
    protected $_tagcollection;
    protected $moduleManager;
    protected $_type;
    protected $_setsFactory;
    protected $_status;
    protected $_visibility;
    protected $_websiteFactory;
    protected $_helper;
	protected $resourceConnection;
    protected $_systemStore;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magebees\Blog\Model\ResourceModel\Tag\Collection $tagcollection,
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
        $this->_tagcollection = $tagcollection;
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
        $this->setId('tagGrid');
        $this->setDefaultSort('tag_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_tagcollection;
        


       
       $this->setCollection($collection);
       
       
        return parent::_prepareCollection();
    }
    

    protected function _prepareColumns()
    {
        $this->addColumn(
            'tag_id',
            [
                'header' => __('ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'tag_id'
            ]
        );
        
        $this->addColumn(
            'title',
            [
                'header' => __('Tag Title'),
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
            'is_active',
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
		$is_imported = $this->_helper->isImported();
	$this->addColumn(
            'is_imported',
            [
                'header' => __('Imported'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'is_imported',
                'type' => 'options',
				'renderer'  => 'Magebees\Blog\Block\Adminhtml\Tag\Edit\Tab\Renderer\IsImported',
                'options' =>  $is_imported
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
                                'field' => 'tag_id'
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
	 protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tagIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('*/*/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['tag_id' => $row->getTagId()]);
    }
	protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }
	    $this->getCollection()->addFieldToFilter('store_id', array('finset' => $value));
    }
	
}
