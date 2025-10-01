<?php
namespace Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab\Optioninfo;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_systemStore;
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magebees\Layerednavigation\Model\Attributeoption $attributeoption,
        array $data = []
    ) {
       
        $this->_request=$context->getRequest();
        $this->attr_helper = $attr_helper;
        $this->helper = $helper;
        $this->attributeoption = $attributeoption;
    
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('option_section');
        $this->setDefaultSort('option_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
         $store_id=(int)$this->getRequest()->getParam('store', 0);
         $attr_id = $this->_request->getParam('id');
        $arr = [];
        $attr_options = $this->attributeoption->getCollection();
        $attr_options->addFieldToFilter('attribute_id', $attr_id)
        ->addFieldToFilter('store_id', $store_id);
        $this->setCollection($attr_options);
        /*below function call for insert attribute option in custom table, if that attribute option added when extension is disable*/
        $this->loadOptionData($attr_id);
        return parent::_prepareCollection();
    }
    public function loadOptionData($attr_id)
    {
        /*This function call for insert attribute option in custom table, if that attribute option added when extension is disable*/
        
        $option_data=$this->attr_helper->getAllOptionsById($attr_id);

        foreach ($option_data as $o) {
                $option_model=$this->attributeoption;
            $opt_collection = $option_model->getCollection()
                    ->addFieldToFilter('attribute_id', $attr_id)
                    ->addFieldToFilter('option_id', $o['value']);
            if (empty($opt_collection->getData())) {
           
				$url_alias=$this->helper->urlAliasAfterReplaceChar($o['label']);
				$main_url_alias=strtolower($o['label']);
                $option_data = [
                'attribute_id' =>$attr_id,
                'option_id'=>$o['value'],
                'url_alias'=>$url_alias,
                'main_url_alias'=>$main_url_alias,
                'option_label'=>$o['label']
                ];
                $option_model->setData($option_data)->save();
            } else {
                $opt_arr=array_column($option_data, 'value');
                $opt_custom_coll =$option_model->getCollection()                                    ->addFieldToFilter('attribute_id', $attr_id);
                $opt_custom_data=$opt_custom_coll->getData();
                $all_opt_arr=array_column($opt_custom_data, 'option_id');
                $option_diff=array_diff($all_opt_arr, $opt_arr);
                foreach ($option_diff as $diff) {
                    $option_model->load($diff, 'option_id');
                    $option_model->delete();
                }
            }
        }
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'option_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'sortable' =>true,
                'index' => 'option_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'option_label',
            [
                'header' => __('Option Label'),
                'type' => 'text',
                'index' => 'option_label',
                'sortable' =>true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'frame_callback'=>[$this,'getOptionLabel']
                    
            ]
        );
        $this->addColumn(
            'url_alias',
            [
                'header' => __('Url Alias for Option'),
                'type' => 'text',
                'index' => 'url_alias',
                'sortable' =>true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
    
    
        return parent::_prepareColumns();
    }
    public function getOptionLabel($value, $row, $column, $isExport)
    {
        
         $store_id=(int)$this->getRequest()->getParam('store', 0);
         $attr_id = $this->_request->getParam('id');
         $option_id=$row->getData('option_id');
         return $this->attr_helper->getOptionLabelStorewise($attr_id, $store_id, $option_id);
    }
    public function getRowUrl($row)
    {
        $option_id=$row->getData('option_id');
        $url=$this->getUrl(
            '*/*/editoption',
            ['id' => $option_id]
        );
        $store_id=(int)$this->getRequest()->getParam('store');
        if ($store_id) {
            $url=$url.'store/'.$store_id;
        }
        //print_r($url);
        return $url;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('layerednavigation/manage/optionsubgrid', ['_current' => true]);
    }
}
