<?php
namespace  Magebees\Layerednavigation\Block\Adminhtml\Brands;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $moduleManager;
    protected $_brands;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        
        $this->moduleManager = $moduleManager;
        $this->_brands = $brands;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('brandsgrid');
        $this->setDefaultSort('brand_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
        
    protected function _prepareCollection()
    {
        $collection = $this->_brands->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'brand_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'brand_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'brand_name',
            [
                'header' => __('Brand Name'),
                'index' => 'brand_name',
                'class' => 'xxx'
            ]
        );
        $this->addColumn(
            'brand_code',
            [
                'header' => __('Attribute Code'),
                'index' => 'brand_code',
                'class' => 'xxx'
            ]
        );
        $this->addColumn(
            'brand_description',
            [
                'header' => __('ToolTip Description'),
                'index' => 'brand_description',
                'class' => 'xxx'
            ]
        );
        
        
        $this->addColumn(
            'filename',
            [
                'header' => __('Thumbnail'),
                'index' => 'filename',
                'align'     =>'left',
                'sortable' =>false,
                'filter' =>false,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'frame_callback'=>[$this,'getImage']
            ]
        );
        
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Position'),
                'index' => 'sort_order',
                'class' => 'xxx'
            ]
        );
        
        $this->addColumn(
            'magebees_edit_layer_brand',
            [
                'header' => __('Edit Brand'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit Brand'),
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
    
    public function getGridUrl()
    {
        return $this->getUrl('*/brands/grid', ['_current' => true]);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/brands/edit',
            ['store' => $this->getRequest()->getParam('brands'), 'id' => $row->getId()]
        );
    }
    public function getImage($value, $row, $column, $isExport)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if ($value) {
            $val=$mediaDirectory.'layernav_brand'.$value;
            $image="<img src=" .$val." height='50px'/>";
            return $image;
        }
    }
}
