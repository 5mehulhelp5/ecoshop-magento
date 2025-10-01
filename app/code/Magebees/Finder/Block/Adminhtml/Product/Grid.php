<?php
namespace Magebees\Finder\Block\Adminhtml\Product;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_finderFactory;
    protected $_mapvalueFactory;
    protected $_dropdownsFactory;
    protected $_finder;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Finder\Model\FinderFactory $finderFactory,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
        array $data = []
    ) {
        $this->_finderFactory = $finderFactory;
        $this->_dropdownsFactory = $dropdownsFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        //$this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    
    public function getFinder()
    {
        if (is_null($this->_finder)) {
            $id = $this->getRequest()->getParam('id');
            $this->_finder = $this->_finderFactory->create()->load($id);
        }
        return $this->_finder;
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_mapvalueFactory->create()->getCollection()->joinFields($this->getFinder());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
        
    /*protected function _prepareMassaction()	{
		$this->setMassactionIdField('vid');
		$this->getMassactionBlock()->setFormFieldName('vid');
		
		$this->getMassactionBlock()->addItem(
				'delete',
				array(
						'label' => __('Delete'),
						'url' => $this->getUrl('finder/product/massdelete'),
						'confirm' => __('Are you sure?'),
						'selected'=>true
				)
		);
		
		return $this;
	}	*/
        
        
    protected function _prepareColumns()
    {
        
        /*$this->addColumn('vid', [
            'header'    => __('ID'),
            'type'      => 'number',
            'index'     => 'vid',
        ]); */
        
        $finderId = $this->getRequest()->getParam('id');
        foreach ($this->getFinder()->getDropdowns($finderId) as $d) {
            $i = $d->getId();
            $this->addColumn(
                "value$i",
                [
                'header'    => $d->getName(),
                'index'     => "value$i",
                'filter_index'     => "d$i.value",
                ]
            );
        }
        
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
            ]
        );
        
        $this->addExportType('*/product/exportCsv', __('CSV'));
        return parent::_prepareColumns();
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/product/grid', ['_current' => true]);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/product/edit',
            ['id' => $row->getVid()]
        );
    }
}
