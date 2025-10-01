<?php

namespace  Magebees\Layerednavigation\Block\Adminhtml\Attribute;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        array $data = []
    ) {
        
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->layerattributeFactory = $layerattributeFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('layerGrid');
        $this->setDefaultSort('attribute_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $frontend_input=['multiselect','select','price','swatch_visual','swatch_text'];
        $productAttributes = $this->productAttributeCollectionFactory->create();
         $productAttributes->addFieldToFilter('is_filterable', ['gt' => 0])->addFieldToFilter('frontend_input', ['in' =>$frontend_input])
        ->addFieldToFilter('is_visible', '1')
        ->addFieldToFilter('is_user_defined', '1');
                
        $this->setCollection($productAttributes);
        
        /*below function call for insert attribute in custom table, if that attribute added when extension is disable*/
        
        //$this->loadAttributeData($productAttributes);
        return parent::_prepareCollection();
    }
        
    public function loadAttributeData($productAttributes)
    {
        /*This function call for insert attribute in custom table, if that attribute added when extension is disable*/
        
        $attribute_collection=$productAttributes->getData();

        foreach ($attribute_collection as $attr_col) {
        /**Start Manage newly added attribute and add in magebees_layernav_attribute table if not exists*/

            $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attr_col['attribute_id']);
            if (!$collection->getSize()) {
                $data = [
                'attribute_id' => $attr_col['attribute_id'],
                'display_mode'=>'0',
                'show_product_count'=>'1',
                'show_searchbox'=>'0',
                'unfold_option'=>'4',
                'always_expand'=>'0',
                'sort_option'=>'0',
                'tooltip'=>'',
                'robots_nofollow'=>'0',
                'robots_noindex'=>'0',
                'rel_nofollow'=>'0'
            
                ];
                $layer_model->setData($data)->save();
            }
        }
    }
    protected function _prepareColumns()
    {
        /*$this->addColumn(
            'attribute_id',
            [
                        'header' => __('ID'),
                        'type' => 'number',
                        'sortable' =>true,
                        'index' => 'attribute_id',
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );*/
        $this->addColumn(
            'attribute_code',
            [
                        'header' => __('Attribute Code'),
                        'type' => 'text',
                        'index' => 'attribute_code',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn(
            'frontend_label',
            [
                        'header' => __('Default Label'),
                        'type' => 'text',
                        'index' => 'frontend_label',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        
        $this->addColumn(
            'magebees_edit_layer_attribute',
            [
                'header' => __('Edit Attribute'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit Attribute'),
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
                'header_css_class' => 'col-action attribute',
                'column_css_class' => 'col-action attribute'
            ]
        );
    
        return parent::_prepareColumns();
    }
    
    
    
    public function getGridUrl()
    {
        return $this->getUrl('layerednavigation/manage/grid', ['_current' => true]);
    }
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'layerednavigation/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
