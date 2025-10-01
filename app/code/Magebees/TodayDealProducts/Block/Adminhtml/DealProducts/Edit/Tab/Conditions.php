<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Conditions extends Generic
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_conditions = $conditions;
        $this->_rendererFieldset = $rendererFieldset;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('todaydeal_data');
                
        /** @var \Magebees\TodayDealProducts\Model\Rule $ruleModel */
        $ruleModel  = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Rule');

        $form = $this->_formFactory->create();
        /** @var \Magento\Framework\Data\Form $form */
        $form->setHtmlIdPrefix('rule_');

        /* start condition */
        if ("" != $model->getData('cond_serialize')) {
            $modelData = $model->getData();
            if (isset($modelData['cond_serialize'])) {
                $ruleModel->setConditions([]);
                $ruleModel->setConditionsSerialized($modelData['cond_serialize']);
                $ruleModel->getConditions()->setJsFormObject('rule_conditions_fieldset');
            }
        }
        
        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('catalog_rule/promo_catalog/newConditionHtml/form/rule_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Create Conditions for Deal Products')]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Product Conditions'), 'title' => __('Product Conditions'), 'required' => true]
        )->setRule(
            $ruleModel
        )->setRenderer(
            $this->_conditions
        );
        /* end condition */
        
     
        $model_data = $model->getData();
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
