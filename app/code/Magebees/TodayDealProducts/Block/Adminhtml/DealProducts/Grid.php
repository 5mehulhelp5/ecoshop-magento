<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts;

use \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\TodayDealProducts\Model\DealFactory $dealFactory,
        array $data = []
    ) {
        $this->_dealFactory = $dealFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('dealGrid');
        $this->setDefaultSort('today_deal_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_dealFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
        
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('today_deal_id');
        $this->getMassactionBlock()->setFormFieldName('todaydeal');
        
        $this->getMassactionBlock()->addItem(
            'display',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('*/*/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        
        $status = [
            ['value' => 1, 'label'=>__('Active')],
            ['value' => 0, 'label'=>__('Inactive')],
        ];

        array_unshift($status, ['label'=>'', 'value'=>'']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('*/*/massStatus', ['_current' => true]),
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
            'today_deal_id',
            [
                'header' => __('Today Deal ID'),
                'type' => 'number',
                'index' => 'today_deal_id',
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
            'from_date',
            [
                'header' => __('Start DateTime'),
                'type' => 'date',
                'index' => 'from_date',
                //'format'=> 'dd/MM/yyyy HH:mm:ss',
            ]
        );
        
        $this->addColumn(
            'to_date',
            [
                'header' => __('End DateTime'),
                'type' => 'date',
                'index' => 'to_date',
                //'format'=> 'dd/MM/yyyy HH:mm:ss',
            ]
        );
        
        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'frame_callback' => [$this, 'decorateStatus'],
                'type' => 'options',
                'options' => [ '0' => 'Inactive', '1' => 'Active'],
            ]
        );
        
        $this->addColumn(
            'edit_deal',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit Deal'),
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
   // public function getGridUrl()
   // {
//        return $this->getUrl('*/*/grid', ['_current' => true]);
  //  }
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {
        if ($value=="Active") {
            $cell = '<span class="grid-severity-notice"><span>Active</span></span>';
        } else {
            $cell = '<span class="grid-severity-minor"><span>Inactive</span></span>';
        }
        return $cell;
    }
}
