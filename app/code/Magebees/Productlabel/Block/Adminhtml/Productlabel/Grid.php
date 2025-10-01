<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel;

use \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Productlabel\Model\ProductlabelFactory $productlabelFactory,
        array $data = []
    ) {
        $this->_productlabelFactory = $productlabelFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productlabelGrid');
        $this->setDefaultSort('label_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_productlabelFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
        
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('label_id');
        $this->getMassactionBlock()->setFormFieldName('label');
        
        $this->getMassactionBlock()->addItem(
            'display',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('productlabel/*/massdelete'),
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
                'url' => $this->getUrl('productlabel/*/massStatus', ['_current' => true]),
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
            'label_id',
            [
                'header' => __('Label ID'),
                'type' => 'number',
                'index' => 'label_id',
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
            'prod_image',
            [
                'header' => __('Product Page Label'),
                'index' => 'prod_image',
                'align' => 'center',
                'renderer'  => '\Magebees\Productlabel\Block\Adminhtml\Productlabel\Renderer\ProductImage',
            ]
        );
        
        $this->addColumn(
            'prod_text',
            [
                'header' => __('Product Page Text'),
                'index' => 'prod_text',
            ]
        );
        $this->addColumn(
            'cat_image',
            [
                'header' => __('Category Page Label'),
                'index' => 'cat_image',
                'align' => 'center',
                'renderer'  => '\Magebees\Productlabel\Block\Adminhtml\Productlabel\Renderer\CategoryImage',
            ]
        );
        $this->addColumn(
            'cat_text',
            [
                'header' => __('Category Page Text'),
                'index' => 'cat_text',
            ]
        );
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Priority'),
                'index' => 'sort_order',
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
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        
        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));
        //$this->addExportType('*/*/exportExcel', __('Excel'));

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
