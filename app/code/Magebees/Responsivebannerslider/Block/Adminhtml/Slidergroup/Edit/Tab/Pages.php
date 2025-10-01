<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab;
class Pages extends \Magento\Backend\Block\Widget\Form\Generic //implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_responsivebannerslider;
	protected $_cmsPage;
	protected $_pages;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
		\Magebees\Responsivebannerslider\Model\Pages $pages,
		\Magento\Cms\Model\Page $cmsPage,
	
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_cmsPage = $cmsPage;
		$this->_responsivebannerslider = $responsivebannerslider;
		$this->_pages = $pages;
		
        parent::__construct($context, $registry, $formFactory, $data);
    }

	protected function Groupsid() {
		 $groups = $this->_cmsPage->getCollection();

		foreach($groups as $group) {
			$data = array(
				'value' => $group->getData('page_id'),
				'label' => $group->getTitle());
			$options[] = $data;		
		}
		return $options;
	}
	
	
    protected function _prepareForm()
    {
		
        $model = $this->_coreRegistry->registry('slidergroup_data');
		$isElementDisabled = false;
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Group Pages')));

		$page_model = $this->_pages->getCollection()
			->addFieldToFilter('slidergroup_id',array('eq' => $model->getId()));
		$page = array();
		foreach($page_model as $page_data){
			$page[] = $page_data->getData('pages');
		}	
		      

		$group_name = $fieldset->addField(
            'pages',
            'multiselect',
            [
                'name' => 'pages[]',
                'label' => __('Visible In'),
                'title' => __('Visible In'),
                'required' => false,
                'values' => $this->Groupsid(),
				'disabled' => $isElementDisabled,
				'value'		=> $page,
            ]
        );
		
        $this->setForm($form);
		 
        return parent::_prepareForm();   
    }

   

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
