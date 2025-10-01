<?php

namespace  Magebees\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab;

class Testimonialinfo extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        $model = $this->_coreRegistry->registry('testimonial');
        $isElementDisabled = false;
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        

        if ($model->getId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Testimonial Detail')]);
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Testimonial Detail')]);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'class'=>'validate-textarea'
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                        'name' => 'email',
                        'label' => __('Customer Email'),
                        'title' => __('Customer Email'),
						'class'=>'validate-email',
                        'required' => true,
                    ]
        );
        $fieldset->addField(
            'company',
            'text',
            [
                        'name' => 'company',
                        'label' => __('Company Name'),
                        'title' => __('Company Name'),
                        'class'=>'validate-textarea'
                        
                    ]
        );
        $fieldset->addField(
            'website',
            'text',
            [
                        'name' => 'website',
                        'label' => __('Company Website'),
                        'title' => __('Company Website'),
                        'class'=>'validate-url'
                        
                    ]
        );
            
        $fieldset->addField(
            'address',
            'textarea',
            [
                        'name' => 'address',
                        'label' => __('Customer Address'),
                        'title' => __('Customer Address'),
                        'class'=>'validate-textarea'
                        
                ]
        );
        $ext_video=$fieldset->addField(
            'ext_video',
            'select',
            [
                'name' => 'ext_video',
                'label' => __('Video Testimonial'),
                'title' => __('Video Testimonial'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $video_url=$fieldset->addField(
            'video_url',
            'text',
            [
                    'name' => 'video_url',
                    'label' => __('Youtube Video URL'),
                    'title' => __('Youtube Video URL'),'class'=>'validate-textarea'
                    
            ]
        );
        $testimonial=$fieldset->addField(
            'testimonial',
            'textarea',
            [
                        'name' => 'testimonial',
                        'label' => __('Testimonial Content'),
                        'title' => __('Testimonial Content'),
                        'required' => true,
                        'class'=>'validate-textarea'
                ]
        );        
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => [
                    '2' => __('Approved'),
                    '1' => __('Not Approved'),
                    '0' => __('Pending'),
                ],
            ]
        );
        $fieldset->addField(
            'rating',
            'select',
            [
                'name' => 'rating',
                'label' => __('Rating'),
                'title' => __('Rating'),
                'values' => [
                    '5' => __('5'),
                    '4' => __('4'),
                    '3' => __('3'),
                    '2' => __('2'),
                    '1' => __('1'),
                ],
            ]
        );
        $fieldset->addField(
            'enabled_home',
            'select',
            [
                'name' => 'enabled_home',
                'label' => __('Display in Home page'),
                'title' => __('Display in Home page'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        $fieldset->addField(
            'enabled_widget',
            'select',
            [
                'name' => 'enabled_widget',
                'label' => __('Display in Widget'),
                'title' => __('Display in Widget'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
                
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        
        $last_field=$fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Profile Picture'),
                'title' => __('Profile Picture'),
                    
            ]
        );
		
		 $dateFormat = $this->_localeDate->getDateFormat(

            \IntlDateFormatter::SHORT

        );
            
		$inserted_date = $fieldset->addField(
            'inserted_date',
            'date',
            [
                'name' => 'inserted_date',
                'label' => __('Date'),
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss', //HH for 24 hours format , hh for 12 hours fomrat
            ]
        );	
			
        if ($model->getData('image')) {
            $model->setData('image', 'testimonial/images'.$model->getData('image'));
        }
        $last_field->setAfterElementHtml('<script>


   require(["jquery"], function ($) {
      require([
"jquery",
"jquery-ui-modules/core","jquery-ui-modules/widget",
"jquery/validate",
"mage/translate"
], function($){
$.validator.addMethod(
"validate-textarea", function (value) {
return (!(/<(.|\n)*?>/g.test(value)));
}, $.mage.__("HTML Tag are not allowed"));
}); 
   });
</script>');
        $form->setValues($model->getData());
        $this->setForm($form);
        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($ext_video->getHtmlId(), $ext_video->getName())
            ->addFieldMap($video_url->getHtmlId(), $video_url->getName())
            ->addFieldDependence(
                $video_url->getName(),
                $ext_video->getName(),
                1
            ));

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Testimonial Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Testimonial Information');
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
