<?php

namespace  Magebees\Testimonial\Block\Adminhtml\Testimonial;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	protected $_testimonialcollectionFactory;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Testimonial\Model\TestimonialcollectionFactory $testimonialcollectionFactory,
        array $data = []
    ) {
        
        $this->_testimonialcollectionFactory = $testimonialcollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('testimonialGrid');
        $this->setDefaultSort('testimonial_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection = $this->_testimonialcollectionFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    
    protected function _prepareMassaction()
    {
        
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');
    
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('testimonial/*/massDelete'),
                        'confirm' => __('Are you sure want to delete?')
                ]
        );
    
        
        return $this;
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'testimonial_id',
            [
                        'header' => __('ID'),
                        'type' => 'number',
                        'sortable' =>true,
                        'index' => 'testimonial_id',
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'name',
            [
                        'header' => __('Customer Name'),
                        'type' => 'text',
                        'index' => 'name',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'email',
            [
                        'header' => __('Customer Email'),
                        'type' => 'text',
                        'index' => 'email',
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
                        'options' => [ '0' => 'Pending', '1' => 'Not Approved','2' => 'Approved'],
                ]
        );
        $this->addColumn(
            'image',
            [
                        'header' => __('Profile Picture'),
                        'index' => 'image',
                        'sortable' =>false,
                        'filter' =>false,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id',
                        'frame_callback'=>[$this,'getImage']
                ]
        );
         $this->addColumn(
             'edit',
             [
                'header' => __('Action'),
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

    
    /*    $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }*/
    
    
        return parent::_prepareColumns();
    }
    
    
    /**
     * @return string
     */
    
    public function getImage($value, $row, $column, $isExport)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if ($value) {
            $val=$mediaDirectory.'testimonial/images'.$value;
            $image="<img src=" .$val." height='50px'/>";
            return $image;
        } else {
            $url=$this->getViewFileUrl("Magebees_Testimonial::images/no_profile.png");
            $image="<img src=" .$url." height='50px'/>";
            return $image;
        }
    }
    public function getStatus($value, $row, $column, $isExport)
    {
        
        if ($value=='Approved') {
            $cell = '<span class="grid-severity-notice"><span>Approved</span></span>';
        } elseif ($value=='Not Approved') {
            $cell = '<span class="grid-severity-major"><span>Not Approved</span></span>';
        } else {
            $cell = '<span class="grid-severity-minor"><span>Pending</span></span>';
        }
        return $cell;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('testimonial/manage/grid', ['_current' => true]);
    }
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'testimonial/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
