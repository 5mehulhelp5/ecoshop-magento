<?php

namespace  Magebees\CacheWarmer\Block\Adminhtml\Warmerlog;

use Magento\Store\Model\Store;
use Magento\Customer\Model\Group;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $warmerLogFactory;
    protected $currencyDir;
    protected $currencyconfig;
    protected $customergroup;
    protected $helper;
	
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\CacheWarmer\Model\LogFactory $warmerLogFactory,
         \Magento\Directory\Model\Currency $currencyDir,
         \Magento\Config\Model\Config\Source\Locale\Currency $currencyconfig,
         \Magento\Customer\Model\Config\Source\Group $customergroup,
         //\Magebees\CacheWarmer\Helper\Data $helper,
		 \Magebees\CacheWarmer\Helper\Http $httpHelper,
        array $data = []
    ) {
        
        $this->warmerLogFactory = $warmerLogFactory;
        $this->currencyDir = $currencyDir;
        $this->currencyconfig = $currencyconfig;
        $this->customergroup = $customergroup;
        //$this->helper = $helper;
		$this->httpHelper = $httpHelper;
         
         
        
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('CacheWarmerGrid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection = $this->warmerLogFactory->create()->getCollection();
		//print_r($collection->getData());
        $this->setCollection($collection);       
        return parent::_prepareCollection();
    }
    
    
   /* protected function _prepareMassaction()
    {
        
        $this->setMassactionIdField('page_id');
        $this->getMassactionBlock()->setFormFieldName('warmerlog');
    
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                        'label' => __('Flush Cache'),
                        'url' => $this->getUrl('cachewarmer//massDelete'),
                        'confirm' => __('Are you sure want to delete?')
                ]
        );
    
        
        return $this;
    }*/
    protected function _prepareColumns()
    {
		$group_result_arr=$this->customergroup->toOptionArray();
		
         array_shift($group_result_arr);

        array_unshift($group_result_arr, [
            'value' => NULL,
            'label' => 'NOT LOGGED IN'
        ]);
         foreach ($group_result_arr as $option) {
            
                $group_arr[$option['value']]= $option['label'];
           
        }
		
		$status_arr = $this->httpHelper->getStatusCodes();
		
		$this->addColumn(
            'created_at',
            [
					'header' => __('Date'),
					'type' => 'text',
					'index' => 'created_at',
					'sortable' =>true,
					'header_css_class' => 'col-id',
					'column_css_class' => 'col-id'
			]
        );
		
		$this->addColumn(
            'url',
            [
                        'header' => __('Url'),
                        'type' => 'text',
                        'index' => 'url',
                        'sortable' =>true,
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
						'frame_callback' => [$this, 'getEmptyStore'],
                        'renderer'=>  'Magento\Backend\Block\Widget\Grid\Column\Renderer\Store',
                        'filter_condition_callback' => [$this, '_filterStoreCondition']
                    ]
                );
				
		$this->addColumn(
            'customer_group',
            [
                        'header' => __('Customer Group'),
                        'type' => 'options',
                        'index' => 'customer_group',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id',
                         'options' =>$group_arr
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

        $this->addColumn(
            'status',
            [
                        'header' => __('Status'),
                        'type' => 'options',
                        'index' => 'status',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id',
                        'options' =>$status_arr
                ]
        );
        $this->addColumn(
            'load_time',
            [
                        'header' => __('Time'),
                        'type' => 'text',
                        'index' => 'load_time',
                        'sortable' =>true,
                        'header_css_class' => 'col-id',
                        'column_css_class' => 'col-id'
                ]
        );
        
		
       
        return parent::_prepareColumns();
    }    
   
    public function getGridUrl()
    {
        return $this->getUrl('magebees_cachewarmer/log/loggrid', ['_current' => true]);
    }
    protected function _filterStoreCondition($collection, $column){

         if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addFieldToFilter('store', array('finset' => $value));
    }
	
	 public function getEmptyStore($value, $row, $column, $isExport)
    {
       if($value){
		   return $value;
	   }else{
		   return "Default";
	   }
        //return $cell;
    }
    
}
