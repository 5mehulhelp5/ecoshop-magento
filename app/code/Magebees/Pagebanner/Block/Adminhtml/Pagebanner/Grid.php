<?php
namespace Magebees\Pagebanner\Block\Adminhtml\Pagebanner;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_listingFactory;
    protected $_systemStore;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Pagebanner\Model\PagebannerFactory $listingFactory,
		\Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_listingFactory = $listingFactory;
		$this->_systemStore = $systemStore;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pagebannerGrid');
        $this->setDefaultSort('banner_id');
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
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banners');
        
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
            ['value' => 1, 'label'=>__('Enabled')],
            ['value' => 0, 'label'=>__('Disabled')],
        ];

        array_unshift($status, ['label'=>'', 'value'=>'']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('*/*/massstatus', ['_current' => true]),
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
            'banner_id',
            [
                'header' => __('Banner ID'),
                'type' => 'number',
                'index' => 'banner_id',
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
        $this->addColumn(
            'page_type_options',
            [
                'header' => __('Page Types'),
                'index' => 'page_type_options',
                //'frame_callback' => [$this, 'decoratePageTypes'],
                'type' => 'options',
				'options' => [
                    '' => __('--Please Select--'),
					'cmspage' => __('CMS Page'),
                    'catalogcategory' => __('Catalog Category'),
                    'blogcategory' => __('Blog Category'),
                    'specifiedpage' => __('Specified Page'),
                ],
            ]
        );
		$this->addColumn(
			'layout_handle',
			[
				'header' => __('Layout Handle'),
				'index' => 'layout_handle',
			]
		);
		
		if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'stores',
                [
                    'header' => __('Store View'),
                    'sortable' => false,
                    'index' => 'stores',
                    'type' => 'options',
                    'options' => $this->_systemStore->getStoreOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }
		$this->addColumn(
			'banner_image',
			array(
				'header' => __('Banner Image'),
				'index' => 'banner_image',
				'renderer'  => '\Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Grid\Renderer\BannerImage',
			)
		);
        $this->addColumn(
            'edit_pagebanner',
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
        if ($value=="Enabled") {
            $cell = '<span class="grid-severity-notice"><span>Enabled</span></span>';
        } else {
            $cell = '<span class="grid-severity-minor"><span>Disabled</span></span>';
        }
        return $cell;
    }
	public function decoratePageTypes($value, $row, $column, $isExport)
    {
		
		$cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
        
        return $cell;
    }
	
}
