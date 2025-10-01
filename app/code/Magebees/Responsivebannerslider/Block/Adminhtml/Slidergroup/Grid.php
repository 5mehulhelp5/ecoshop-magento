<?php
/***************************************************************************
 Extension Name  : Magento2 Responsive Banner Slider with Lazy Load Extension
 Extension URL   : http://www.magebees.com/magento2-responsive-banner-slider-with-lazy-load-extension.html
 Copyright    : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email   : support@magebees.com 
 ***************************************************************************/
 ?>
<?php
namespace  Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup;
use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	
	protected $moduleManager;
	protected $_responsivebannerslider;
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Backend\Helper\Data $backendHelper,
			\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
			\Magento\Framework\Module\Manager $moduleManager,
		
			array $data = array()
	) {
	
		$this->moduleManager = $moduleManager;
		$this->_responsivebannerslider = $responsivebannerslider;
				
		parent::__construct($context, $backendHelper, $data);
	}
	
	protected function _construct()
	{
		parent::_construct();
		$this->setId('slidergroupgrid');
		$this->setDefaultSort('slidergroup_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		
	}
		
	protected function _prepareCollection()
	{
		$collection = $this->_responsivebannerslider->getCollection();
		$storeId = (int)$this->getRequest()->getParam('store', 0);
		if($storeId){
			$collection->storeFilter($storeId);
		}
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

  	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('slidergroup_id');
		$this->getMassactionBlock()->setFormFieldName('slidergroup');
		
		$this->getMassactionBlock()->addItem(
				'display',
				array(
						'label' => __('Delete'),
						'url' => $this->getUrl('responsivebannerslider/*/massdelete'),
						'confirm' => __('Are you sure?'),
						'selected'=>true
				)
		);
		
		$statuses = $this->_responsivebannerslider->getAvailableStatuses();

		array_unshift($statuses, array('label' => '', 'value' => ''));
		$this->getMassactionBlock()->addItem(
           'status',
            array(
                'label' => __('Change status'),
                'url' => $this->getUrl('responsivebannerslider/*/massStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    )
                )
            )
        );
				
		return $this;
	}

	protected function _prepareColumns()
    {
        $this->addColumn(
            'slidergroup_id',
            array(
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slidergroup_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => __('Group Title'),
                'index' => 'title',
                'class' => 'xxx'
            )
        );
		$this->addColumn(
            'sort_order',
            array(
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'class' => 'xxx'
            )
        );
		$this->addColumn(
            'status',
            array(
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options'  => array(
				1 => 'Enabled',
				2 => 'Disabled',
				),
            )
        );
		$this->addColumn(
            'edit',
            array(
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('Edit Group'),
                        'url' => array(
                            'base' => '*/*/edit',
                            'params' => array('store' => $this->getRequest()->getParam('store'))
                        ),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            )
        );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
 	
	public function getGridUrl()
	{
		return $this->getUrl('*/slidergroup/grid', array('_current' => true));
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl(
				'*/slidergroup/edit',
				array('store' => $this->getRequest()->getParam('slidergroup'), 'id' => $row->getId())
		);
	}

	public function getGroupData() {
		$groups = $this->_responsivebannerslider->getCollection()->setOrder('slidergroup_id', 'ASC');
	    if(count($groups) > 0) {
			foreach($groups as $group) {
				$options[$group->getData('slidergroup_id')] = $group->getTitle();
			}
			return $options;
		 }
		else{
			return false;
		} 			
    }
 }