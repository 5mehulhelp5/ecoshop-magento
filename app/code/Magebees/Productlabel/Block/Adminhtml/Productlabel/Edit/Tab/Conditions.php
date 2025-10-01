<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit\Tab;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_catalogProduct;
    protected $_eavColletion;
       
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
        \Magebees\Productlabel\Model\RuleFactory $ruleFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_conditions = $conditions;
		 $this->ruleFactory = $ruleFactory;
        $this->_rendererFieldset = $rendererFieldset;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('productlabel_data');
		
		/** @var \Magebees\Productlabel\Model\Rule $ruleModel */
        $ruleModel  = $this->ruleFactory->create();
		
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        
       /* start condition */
        if ("" != $model->getData('cond_serialize')) {
            $modelData = $model->getData();
            if (isset($modelData['cond_serialize'])) {
                $ruleModel->setConditions([]);
                $ruleModel->setConditionsSerialized($modelData['cond_serialize']);
                $ruleModel->getConditions()->setJsFormObject('conditions_fieldset');
            }
        }
        
		$renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('catalog_rule/promo_catalog/newConditionHtml/form/conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Conditions')]
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
        
        //Is New or Is On Sale
        $fieldset = $form->addFieldset('new_form', ['legend'=>__('Is New or Is On Sale')]);
        
        $fieldset->addField(
            'is_new',
            'select',
            [
                'label'     => ('Is New'),
                'name'      => 'is_new',
                'values'    => [
                    0 => __('-- Please Select --'),
                    1 => __('No'),
                    2 => __('Yes'),
                ],
            ]
        );

        $fieldset->addField(
            'is_sale',
            'select',
            [
                'label'     => ('Is on Sale'),
                'name'      => 'is_sale',
                'values'    => [
                    0 => __('-- Please Select --'),
                    1 => __('No'),
                    2 => __('Yes'),
                ],
            ]
        );

        //Date Range
        $fieldset = $form->addFieldset('date_range_form', ['legend'=>__('Date Range')]);
        
        $date_enabled = $fieldset->addField(
            'date_enabled',
            'select',
            [
                'label'     => __('Use Date Range'),
                'name'      => 'date_enabled',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
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
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
        
        //Price Range
        $fieldset = $form->addFieldset('price_range_form', ['legend'=>__('Price Range')]);
        
        $price_enabled = $fieldset->addField(
            'price_enabled',
            'select',
            [
                'label'     => __('Use Price Range'),
                'title'     => __('Use Price Range'),
                'name'      => 'price_enabled',
                'options'   => [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
            ]
        );
        
        $by_price = $fieldset->addField(
            'by_price',
            'select',
            [
                'label'     => __('By Price'),
                'title'     => __('By Price'),
                'name'      => 'by_price',
                'options'   => [
                    '0' => __('Base Price'),
                    '1' => __('Special Price'),
                    '2' => __('Final Price'),
                    '3' => __('Final Price Incl Tax'),
                ],
            ]
        );
        
        $from_price = $fieldset->addField(
            'from_price',
            'text',
            [
                'name'   => 'from_price',
                'label'  => __('From Price'),
                'title'  => __('From Price'),
                'class'  => 'validate-zero-or-greater',
            ]
        );
        
        $to_price = $fieldset->addField(
            'to_price',
            'text',
            [
                'name'   => 'to_price',
                'label'  => __('To Price'),
                'title'  => __('To Price'),
                'class'  => 'validate-zero-or-greater',
            ]
        );
        
        //Stock Status
        $fieldset = $form->addFieldset(
            'stock_form',
            ['legend'=>__('Stock Status')]
        );
        
        $fieldset->addField(
            'stock_status',
            'select',
            [
                'label'     => __('Status'),
                'name'      => 'stock_status',
                'values'    => [
                    '0' => __('-- Please Select --'),
                    '1' => __('In Stock'),
                    '2' => __('Out of Stock'),
                 ],
            ]
        );
        
        $model_data = $model->getData();
        $form->setValues($model_data);
        $this->setForm($form);
        
        
        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($to_price->getHtmlId(), $to_price->getName())
            ->addFieldMap($from_price->getHtmlId(), $from_price->getName())
            ->addFieldMap($by_price->getHtmlId(), $by_price->getName())
            ->addFieldMap($price_enabled->getHtmlId(), $price_enabled->getName())
            ->addFieldMap($to_date->getHtmlId(), $to_date->getName())
            ->addFieldMap($from_date->getHtmlId(), $from_date->getName())
            ->addFieldMap($date_enabled->getHtmlId(), $date_enabled->getName())
            ->addFieldDependence(
                $to_date->getName(),
                $date_enabled->getName(),
                1
            )
            ->addFieldDependence(
                $from_date->getName(),
                $date_enabled->getName(),
                1
            )
            ->addFieldDependence(
                $to_price->getName(),
                $price_enabled->getName(),
                1
            )
            ->addFieldDependence(
                $from_price->getName(),
                $price_enabled->getName(),
                1
            )
            ->addFieldDependence(
                $by_price->getName(),
                $price_enabled->getName(),
                1
            ));
        
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
