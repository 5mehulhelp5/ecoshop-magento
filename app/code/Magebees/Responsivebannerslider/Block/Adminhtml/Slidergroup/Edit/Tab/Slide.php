<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Slide extends Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	protected $_coreRegistry = null;
	protected $moduleManager;
	protected $_slide;
 	
 	public function __construct(
 			\Magento\Backend\Block\Template\Context $context,
 			\Magento\Framework\Module\Manager $moduleManager,
			\Magento\Backend\Helper\Data $backendHelper,
 			\Magebees\Responsivebannerslider\Model\Slide $slide,
 			\Magento\Framework\Registry $coreRegistry,
 			array $data = []
 	) {
 		
 		$this->_coreRegistry = $coreRegistry;
 		$this->moduleManager = $moduleManager;
		$this->_slide = $slide;
		
 		parent::__construct($context, $backendHelper, $data);
 	}
 
 	protected function _construct()
  	{
  		parent::_construct();
 		$this->setId('slidergroup_slide_section');
 		$this->setDefaultSort('slide_id');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
 	protected function _prepareCollection()
 	{
		$model = $this->_coreRegistry->registry('slidergroup_data');
		$collection = $this->_slide->getCollection();
		$id = $this->getRequest()->getParam('id');
			if($id)	{
				$id = $id;
			}else{
				$id = $model->getId();
			}
		
		$collection->addFieldToFilter('group_names', array(array('finset' => $id)));
		$this->setCollection($collection);
        return parent::_prepareCollection();
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
 	
 
 		
 		return parent::_prepareColumns();
 	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/slidegrids', ['_current' => true]);
	}
 
 	 public function getTabLabel()
    {
        return __('Slides of this Groups');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Slides of this Groups');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
		$model = $this->_coreRegistry->registry('slidergroup_data');
		if ($model->getId()) {
           return true;
        }else{
			return false;
		}	
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
 	
	public function getRowUrl($row)
	{
		return $this->getUrl(
				'*/slide/edit',
				array('store' => $this->getRequest()->getParam('slide'), 'id' => $row->getId())
		);
	}
	
 }
