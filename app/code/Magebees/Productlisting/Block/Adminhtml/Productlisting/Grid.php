<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_listingFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Productlisting\Model\ProductlistingFactory $listingFactory,
        array $data = []
    ) {
        $this->_listingFactory = $listingFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productlistingGrid');
        $this->setDefaultSort('listing_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_listingFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
        
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('listing_id');
        $this->getMassactionBlock()->setFormFieldName('listing');
        
        $this->getMassactionBlock()->addItem(
            'display',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('prodlist/*/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        
        $status = [
            ['value' => 1, 'label'=>__('Enabled')],
            ['value' => 0, 'label'=>__('Disabled')],
        ];

        array_unshift($status, ['label'=>'', 'value'=>'']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('prodlist/*/massStatus', ['_current' => true]),
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
            'listing_id',
            [
                'header' => __('Listing ID'),
                'type' => 'number',
                'index' => 'listing_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
            ]
        );		
        
        
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'frame_callback' => [$this, 'decorateStatus'],
                'type' => 'options',
                'options' => [ '0' => 'Disabled', '1' => 'Enabled'],
            ]
        );
		
		/*$this->addColumn(
			'stores',
			[
				'header' => __('Store Views'),
				'index' => 'stores',                        
				'type' => 'store',
				'store_all' => true,
				'store_view' => true,
				'renderer'=>  'Magento\Backend\Block\Widget\Grid\Column\Renderer\Store',
				'filter_condition_callback' => [$this, '_filterStoreCondition']
			]
		);*/
        
        $this->addColumn(
            'edit_listing',
            [
                'header' => __('Edit Listing'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit Listing'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        
        return parent::_prepareColumns();
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }
	
	protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addFieldToFilter('stores', array('finset' => $value));
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {
        if ($value=="Enabled") {
            $cell = '<span class="grid-severity-notice"><span>Enabled</span></span>';
        } else {
            $cell = '<span class="grid-severity-minor"><span>Disabled</span></span>';
        }
        return $cell;
    }
}
