<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab;
class Code extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_responsivebannerslider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
			
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_responsivebannerslider = $responsivebannerslider;
		$this->setTemplate('Magebees_Responsivebannerslider::responsivebannerslider/code.phtml');
		
        parent::__construct($context, $registry, $formFactory, $data);
    }

	public function getCurrentGroup() {
		$model = $this->_coreRegistry->registry('slidergroup_data');
		return $model->getData();    
	}
	
    protected function _prepareForm()
    {
	
        $model = $this->_coreRegistry->registry('slidergroup_data');
		$isElementDisabled = false;
        
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Group Pages')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		
        $form->setValues($model->getData());
        $this->setForm($form);
		 
        return parent::_prepareForm();   
    } 

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Use Code Inserts');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Use Code Inserts');
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
}
