<?php

namespace  Magebees\Advertisementblock\Block\Adminhtml\Advertisementblock;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{	
	protected $_AdvertisementinfoFactory;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Advertisementblock\Model\AdvertisementinfoFactory $AdvertisementinfoFactory,
        array $data = []
    ) {
        
        $this->_AdvertisementinfoFactory = $AdvertisementinfoFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('AdvertisementblockGrid');
        $this->setDefaultSort('advertisement_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection = $this->_AdvertisementinfoFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    
    protected function _prepareMassaction()
    {
        
        $this->setMassactionIdField('advertisement_id');
        $this->getMassactionBlock()->setFormFieldName('advertisementblock');
    
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('advertisementblock/*/massDelete'),
                        'confirm' => __('Are you sure want to delete?')
                ]
        );
    
        
        return $this;
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'advertisement_id',
            [
                        'header' => __('ID'),
                        'type' => 'number',
                        'sortable' =>true,
                        'index' => 'advertisement_id',
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'block_name',
            [
                        'header' => __('Advertisement Block Name'),
                        'type' => 'text',
                        'index' => 'block_name',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'pattern',
            [
                        'header' => __('Pattern'),
                        'type' => 'text',
                        'index' => 'pattern',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'status',
            [
                        'header' => __('Status'),
                        'type' => 'options',
                        'index' => 'status',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id',
                        'frame_callback'=>[$this,'getStatus'],
                        'options' => [ '0' => 'Disabled', '1' => 'Enabled'],
                ]
        );
    
         $this->addColumn(
             'edit',
             [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
                    ]
                ],
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'filter' => false,                
                'column_css_class' => 'col-action'
             ]
         );

    
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
    
    
        return parent::_prepareColumns();
    }
    
    public function getStatus($value, $row, $column, $isExport)
    {
        
        if ($value=='Enabled') {
            $cell = '<span class="grid-severity-notice"><span>Enabled</span></span>';
        } elseif ($value=='Disabled') {
            $cell = '<span class="grid-severity-major"><span>Disabled</span></span>';
        }
        
        return $cell;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('advertisementblock/manage/grid', ['_current' => true]);
    }
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'advertisementblock/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
