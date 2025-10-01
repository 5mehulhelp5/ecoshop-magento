<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
       
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('todaydeal_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('today_deal_id', 'hidden', ['name' => 'today_deal_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'description',
            'text',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
            ]
        );
        
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => [
                    '1' => __('Active'),
                    '0' => __('Inactive'),
                ],
            ]
        );
                
        $timer_format = $fieldset->addField(
            'timer_format',
            'select',
            [
                'name' => 'timer_format',
                'label' => __('Timer Format'),
                'title' => __('Timer Format'),
                'values' => [
                    '0' => __('24 hours'),
                    '1' => __('Based on From and To Date'),
                ],
            ]
        );
        
        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        
        $from_date = $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss', //HH for 24 hours format , hh for 12 hours fomrat
                'required' => true,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
        
        $to_date = $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To Date'),
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss',
                'required' => true,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $group = $om->get('Magento\Customer\Model\Group');
        
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' => $group->getCollection()->toOptionArray(),
            ]
        );
        
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'select_stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
            
		$fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => false,
                'class'=> 'validate-digits',
                'note'      => __('0 = High Priority'),
            ]
        );
			
        $model_data = $model->getData();
		$model_data['select_stores'] = $model->getStores();
        $form->setValues($model_data);
        $this->setForm($form);
            
        return parent::_prepareForm();
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
