<?php
namespace Magebees\Finder\Block\Adminhtml\Import;

class HistoryGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_finderFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magebees\Finder\Model\HistoryFactory $historyFactory,
        array $data = []
    ) {
        $this->_historyFactory = $historyFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('histroy_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_historyFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
        
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('history_id');
        $this->getMassactionBlock()->setFormFieldName('history');
        
        $this->getMassactionBlock()->addItem(
            'display',
            [
                        'label' => __('Delete'),
                        'url' => $this->getUrl('finder/import/massdelete'),
                        'confirm' => __('Are you sure?'),
                        'selected'=>true
                ]
        );
        
        return $this;
    }
        
    protected function _prepareColumns()
    {
        $this->addColumn(
            'history_id',
            [
                'header' => __('History ID'),
                'type' => 'number',
                'index' => 'history_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'filename',
            [
                'header' => __('File Name'),
                'index' => 'filename',
            ]
        );
		
		$this->addColumn(
		  'started_time',
			[
			  'header' => __('Started'),
			  'index' => 'started_time',
			  'type' => 'datetime'
			]
		);
		
		$this->addColumn(
		  'finished_time',
			[
			  'header' => __('Finished'),
			  'index' => 'finished_time',
			  'type' => 'datetime'
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

}
