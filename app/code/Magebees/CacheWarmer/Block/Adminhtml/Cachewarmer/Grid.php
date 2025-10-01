<?php

namespace  Magebees\CacheWarmer\Block\Adminhtml\Cachewarmer;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $warmerQueueFactory;
	
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\CacheWarmer\Model\Queue\PageFactory $warmerQueueFactory,
        array $data = []
    ) {
        
        $this->warmerQueueFactory = $warmerQueueFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('CacheWarmerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
         
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection = $this->warmerQueueFactory->create()->getCollection();
		//print_r($collection->getData());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	
    protected function _prepareColumns()
    {
        
        $this->addColumn(
            'url',
            [
                        'header' => __('Url'),
                        'type' => 'text',
                        'index' => 'url',
                        'sortable' =>true,
						'frame_callback' => [$this, 'redirectUrl'],
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
		
		$this->addColumn(
                    'store',
                    [
                        'header' => __('Store Views'),
                        'index' => 'store',                        
                        'type' => 'store',
                        'store_all' => true,
                        'store_view' => true,
                        'renderer'=>  'Magento\Backend\Block\Widget\Grid\Column\Renderer\Store',
                        'filter_condition_callback' => [$this, '_filterStoreCondition']
                    ]
                );
		
        $this->addColumn(
            'rate',
            [
                        'header' => __('Rate'),
                        'type' => 'text',
                        'index' => 'rate',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        
        

    
    
        return parent::_prepareColumns();
    }    
    public function getGridUrl()
    {
        return $this->getUrl('magebees_cachewarmer/queue/grid', ['_current' => true]);
    }
    
	protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addFieldToFilter('store', array('finset' => $value));
    }
	
	public function redirectUrl($value, $row, $column, $isExport)
    {
        $html = '<a target="_blank" href="'.$value.'">'.$value. '</a>';        
        return $html;
    }
}
