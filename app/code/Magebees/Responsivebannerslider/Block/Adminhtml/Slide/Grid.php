<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slide;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
	protected $_responsivebannerslider;
	protected $_slide;
	
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Backend\Helper\Data $backendHelper,
			\Magebees\Responsivebannerslider\Model\Slide $slide,
			\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
			\Magento\Framework\Module\Manager $moduleManager,
		
			array $data = array()
	) {
		$this->moduleManager = $moduleManager;
		$this->_slide = $slide;
		$this->_responsivebannerslider = $responsivebannerslider;
		
		parent::__construct($context, $backendHelper, $data);
	}
	
	protected function _construct()
	{
		parent::_construct();
		$this->setId('sliderid');
		$this->setDefaultSort('slide_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		
	}
		
	protected function _prepareCollection()
	{
		$collection = $this->_slide->getCollection();
		$groupId = (int) $this->getRequest()->getParam('group');
		if($groupId != 0) {
			$collection->addFieldToFilter('group_names', array(array('finset' => $groupId)));  
		}
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
  
	protected function _prepareMassaction()
	{
	
		$this->setMassactionIdField('slide_id');
		$this->getMassactionBlock()->setFormFieldName('slide');
	
	
		$this->getMassactionBlock()->addItem(
				'display',
				array(
						'label' => __('Delete'),
						'url' => $this->getUrl('responsivebannerslider/slide/massdelete'),
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
                        'name' => 'statuss',
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
            'slide_id',
            array(
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slide_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'titles',
            array(
                'header' => __('Group Title'),
                'index' => 'titles',
                'class' => 'xxx'
            )
        );
		
		$this->addColumn(
			'Group',
			array(
				'header' => __('Group'),
				'index' => 'group_names',
				'align'     =>'left',
				'renderer'  => '\Magebees\Responsivebannerslider\Block\Adminhtml\Slide\Renderer\Groups',
			)
		);  
		$this->addColumn(
            'statuss',
            array(
                'header' => __('Status'),
                'index' => 'statuss',
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
                        'caption' => __('Edit Slide'),
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
		return $this->getUrl('*/slide/grid', array('_current' => true));
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl(
				'*/slide/edit',
				array('store' => $this->getRequest()->getParam('slide'), 'id' => $row->getId())
		);
	}

}