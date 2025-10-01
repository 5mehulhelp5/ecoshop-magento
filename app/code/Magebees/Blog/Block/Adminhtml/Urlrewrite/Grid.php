<?php
namespace  Magebees\Blog\Block\Adminhtml\Urlrewrite;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
   
    protected $_productFactory;
	protected $_urlcollection;
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
        \Magebees\Blog\Model\ResourceModel\UrlRewrite\Collection $urlcollection,
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
        $this->_urlcollection = $urlcollection;
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
        $this->setId('urlrewriteGrid');
        $this->setDefaultSort('url_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
      
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_urlcollection;
		 $this->setCollection($collection);
       
        return parent::_prepareCollection();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('url_id');
        $this->getMassactionBlock()->setFormFieldName('urlIds');
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

    protected function _prepareColumns()
    {
        $this->addColumn(
            'url_id',
            [
                'header' => __('ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'url_id'
            ]
        );
        
        $this->addColumn(
            'old_url',
            [
                'header' => __('Old URL'),
                'align' => 'left',
                'index' => 'old_url'
            ]
        );
        $this->addColumn(
            'new_url',
            [
                'header' => __('New URL'),
                'align' => 'left',
                'index' => 'new_url'
            ]
        );
		$this->addColumn(
            'type',
            [
                'header' => __('URL Type'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'type',
				'type' => 'options',
				 'options' => $this->_helper->getRewriteUrlType()
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
                                'field' => 'url_id'
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
        return $this->getUrl('*/*/edit', ['url_id' => $row->getUrlId()]);
    }
	
}
