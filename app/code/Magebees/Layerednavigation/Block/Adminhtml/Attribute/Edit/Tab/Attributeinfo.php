<?php

namespace  Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab;

class Attributeinfo extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_fieldFactory = $fieldFactory;
         $this->_eavAttribute = $eavAttribute;
        $this->_scopeConfig=$context->getScopeConfig();
        parent::__construct($context, $registry, $formFactory, $data);
    }
   
    protected function _prepareForm()
    {
         $default_config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $default_swatch_template=$default_config['default_swatch_config'];
        $multiselect_swatch=$default_config['multiselect_swatch'];
                
        $model = $this->_coreRegistry->registry('layer_attribute');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        if ($model->getId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Attribute Detail')]);
            $fieldset->addField('attribute_id', 'hidden', ['name' => 'attribute_id']);
        }
        $model_data=$model->getData();
        $attribute_id=$model_data['attribute_id'];
        $attribute_detail=$this->_eavAttribute->load($attribute_id);
        $attribute_data=$attribute_detail->getData();
       
        if ((!isset($attribute_data['additional_data'])) || ($default_swatch_template==0)) {
            $display_mode=$fieldset->addField(
                'display_mode',
                'select',
                [
                'name' => 'display_mode',
                'label' => __('Display Mode'),
                'title' => __('Display Mode'),
                'values' => [
                    '6' => __('Radio Button With Labels'),
                    '5' => __('Multiselect With Labels and Images'),
                    '4' => __('Multiselect With Labels Only'),
                    '3' => __('Drop-down'),
                    '2' => __('Images and Labels'),
                    '1' => __('Images Only'),
                    '0' => __('Default(Labels Only)'),
                ],
                //'after_element_html' => $afterElementHtml,
                ]
            );
        }
        $fieldset->addField(
            'show_in_block',
            'select',
            [
                'name' => 'show_in_block',
                'label' => __('Show In Block'),
                'title' => __('Show In Block'),
                'values' => [
                    '2' => __('Both'),
                    '1' => __('Top'),
                    '0' => __('Sidebar'),
                ],
            ]
        );
        if ((!isset($attribute_data['additional_data'])) || ($default_swatch_template==0)) {
            $fieldset->addField(
                'show_product_count',
                'select',
                [
                'name' => 'show_product_count',
                'label' => __('Show Product Count'),
                'title' => __('Show Product Count'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
                //'after_element_html' => $afterElementHtml,
                ]
            );
        }
        
        if ((!isset($attribute_data['additional_data'])) || ($default_swatch_template==0)) {
            $fieldset->addField(
                'show_searchbox',
                'select',
                [
                'name' => 'show_searchbox',
                'label' => __('Show Searchbox'),
                'title' => __('Show Searchbox'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
                //'after_element_html' => $afterElementHtml,
                ]
            );
        }

        if ((!isset($attribute_data['additional_data'])) || ($default_swatch_template==0)) {
            $fieldset->addField(
                'unfold_option',
                'text',
                [
                        'name' => 'unfold_option',
                        'label' => __('Number of Unfold Option'),
                        'title' => __('Number of Unfold Option'),
                        'required' => true,
                //'after_element_html' => $afterElementHtml,
                    ]
            );
        }
        
        $fieldset->addField(
            'always_expand',
            'select',
            [
                'name' => 'always_expand',
                'label' => __('Collapse Attribute'),
                'title' => __('Collapse Attribute'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $fieldset->addField(
            'sort_option',
            'select',
            [
                'name' => 'sort_option',
                'label' => __('Sort Option By'),
                'title' => __('Sort Option By'),
                'values' => [
                    '2' => __('Product Count'),
                    '1' => __('Name'),
                    '0' => __('Position'),
                ],
            ]
        );
        $fieldset->addField(
            'tooltip',
            'text',
            [
                'name' => 'tooltip',
                'label' => __('Tool-Tip'),
                'title' => __('Tool-Tip'),
                //'after_element_html' => $afterElementHtml,
            ]
        );
        if ((!isset($attribute_data['additional_data'])) || ($multiselect_swatch==1) || ($default_swatch_template==0)) {
            $and_logic=$fieldset->addField(
                'and_logic',
                'select',
                [
                'name' => 'and_logic',
                'label' => __('Use AND logic for multiple selections'),
                'title' => __('Use AND logic for multiple selections'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
                ]
            );
        }
        
    
        $seo_detail = $form->addFieldset('seo_fieldset', ['legend' => __('SEO')]);
        $seo_detail->addField(
            'robots_nofollow',
            'select',
            [
                'name' => 'robots_nofollow',
                'label' => __('Robots NoFollow Tag'),
                'title' => __('Robots NoFollow Tag'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $seo_detail->addField(
            'robots_noindex',
            'select',
            [
                'name' => 'robots_noindex',
                'label' => __('Robots NoIndex Tag'),
                'title' => __('Robots NoIndex Tag'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $seo_detail->addField(
            'rel_nofollow',
            'select',
            [
                'name' => 'rel_nofollow',
                'label' => __('Rel NoFollow'),
                'title' => __('Rel NoFollow'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $cases = $form->addFieldset('case_fieldset', ['legend' => __('Apply Condition')]);
        $cases->addField(
            'include_cat',
            'text',
            [
                'name' => 'include_cat',
                'label' => __('Include Only In Categories'),
                'title' => __('Include Only In Categories')     ,
                'after_element_html' =>  'Comma separated list of the categories IDs like 2,4,6'
            ]
        );
        
        $cases->addField(
            'exclude_cat',
            'text',
            [
                'name' => 'exclude_cat',
                'label' => __('Exclude From Categories'),
                'title' => __('Exclude From Categories'),
                'after_element_html' =>  'Comma separated list of the categories IDs like 2,4,6'
            ]
        );
        

        $form->setValues($model->getData());
        $this->setForm($form);
        $refField = $this->_fieldFactory->create(
            ['fieldData' => ['value' => '4,5', 'separator' => ','], 'fieldPrefix' => '']
        );
        if ((!isset($attribute_data['additional_data'])) || ($default_swatch_template==0)) {
            $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($display_mode->getHtmlId(), $display_mode->getName())
            ->addFieldMap($and_logic->getHtmlId(), $and_logic->getName())
            ->addFieldDependence(
                $and_logic->getName(),
                $display_mode->getName(),
                $refField
            ));
        }
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attribute Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Improved Layered Navigation');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
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
