<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab;
class Main extends \Magento\Backend\Block\Widget\Form\Generic // implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
 
    protected $_systemStore;
	protected $_responsivebannerslider;
	protected $_yesno;
	protected $_store;
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
		\Magebees\Responsivebannerslider\Model\Store $store,
		\Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_responsivebannerslider = $responsivebannerslider;
		$this->_store = $store;
		$this->_yesno = $yesno;
        parent::__construct($context, $registry, $formFactory, $data);
    }

 
    protected function _prepareForm()
    {
		
        $model = $this->_coreRegistry->registry('slidergroup_data');
		$isElementDisabled = false;
  
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('General information')));

        if ($model->getId()) {
            $fieldset->addField('slidergroup_id', 'hidden', array('name' => 'slidergroup_id'));
        }

		$title = $fieldset->addField(
            'title',
            'text',
            array(
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            )
        );
		$position = $fieldset->addField(
            'position',
            'select',
            array(
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'values' => $this->_responsivebannerslider->getPosition(),
            )
        );
		$sort_order = $fieldset->addField(
            'sort_order',
            'text',
            array(
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
				'class'     => 'validate-number',
                'note' => 'set the sort order in case of multiple group on one page.'
            )
        );
		$status = $fieldset->addField(
            'status',
            'select',
            array(
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_responsivebannerslider->getAvailableStatuses(),
            )
        );
		
		 if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
		
        
		$fieldset = $form->addFieldset('effect_form', array('legend' => __('Slider Effect')));


        $lazy_load = $fieldset->addField(
            'lazy_load',
            'select',
            [
                'name' => 'lazy_load',
                'label' => __('Lazy load'),
                'title' => __('Lazy load'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
		
		$start_animation = $fieldset->addField(
            'start_animation',
            'select',
            [
                'name' => 'start_animation',
                'label' => __('Auto Start Animation'),
                'title' => __('Auto Start Animation'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
			]
        );
		
		$loop_slider = $fieldset->addField(
            'loop_slider',
            'select',
            [
                'name' => 'loop_slider',
                'label' => __('Loop Slider '),
                'title' => __('Loop Slider '),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
			]
        );
		
		$animation_type = $fieldset->addField(
            'animation_type',
            'select',
            [
                'name' => 'animation_type',
                'label' => __('Animation Type'),
                'title' => __('Animation Type'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getAnimationType(),
			]
        );		
		
		$animation_direction = $fieldset->addField(
            'animation_direction',
            'select',
            [
                'name' => 'animation_direction',
                'label' => __('Animation Direction'),
                'title' => __('Animation Direction'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getAnimationDirection(),
			]
        );
		
		$slide_duration = $fieldset->addField(
            'slide_duration',
            'text',
            array(
                'name' => 'slide_duration',
                'label' => __('Slide Duration'),
                'title' => __('Slide Duration'),
                'required' => true,
            )
        );	
		
		
		$smooth_height = $fieldset->addField(
            'smooth_height',
            'select',
            [
                'name' => 'smooth_height',
                'label' => __('Smooth Height'),
                'title' => __('Smooth Height'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
			]
        );

        $scrollbar = $fieldset->addField(
            'scrollbar',
            'select',
            [
                'name' => 'scrollbar',
                'label' => __('Scrollbar Effect'),
                'title' => __('Scrollbar Effect'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );

        $keyboard = $fieldset->addField(
            'keyboard',
            'select',
            [
                'name' => 'keyboard',
                'label' => __('Keyboard control'),
                'title' => __('Keyboard control'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
        

        $grab = $fieldset->addField(
            'grab',
            'select',
            [
                'name' => 'grab',
                'label' => __('Grab cursor'),
                'title' => __('Grab cursor'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
    


        $space_between = $fieldset->addField(
            'space_between',
            'text',
            array(
                'name' => 'space_between',
                'label' => __('Space Between Two Slide'),
                'title' => __('Space Between Two Slide'),
                'required' => true,
            )
        );  
		
		$fieldset = $form->addFieldset('style_form', array('legend' => __('Slider Style')));
		
		$max_width = $fieldset->addField(
            'max_width',
            'text',
            array(
                'name' => 'max_width',
                'label' => __('Maximum Width Slider'),
                'title' => __('Maximum Width Slider'),
                'required' => false,
				'note' => 'maximum width of the slider in pixels, leave empty or 0 for full responsive width.',
            )
        );
		
		
		
		$slider_type = $fieldset->addField(
            'slider_type',
            'select',
            [
                'name' => 'slider_type',
                'label' => __('Slider Type'),
                'title' => __('Slider Type'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getType(),
			]
        );

        $slides_per_view = $fieldset->addField(
            'slides_per_view',
            'text',
            array(
                'name' => 'slides_per_view',
                'label' => __('Slides per view'),
                'title' => __('Slides per view'),
                'required' => false,
                
            )
        );

        
        $colourpickerRenderer = $this->getLayout()->createBlock(
        'Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Color'
    );
		$content_background = $fieldset->addField(
            'content_background',
            'text',
            array(
                'name' => 'content_background',
                'label' => __('Banner Content Background'),
               // 'title' => __('Banner content background'),
                'required' => false,
				'class' => "bgcontent",
			)
        );
 $content_background->setRenderer($colourpickerRenderer);


       
        $banner_content_tcolor = $fieldset->addField(
            'banner_content_tcolor',
            'text',
            array(
                'name' => 'banner_content_tcolor',
                'label' => __('Banner Content Text Color'),
               // 'title' => __('Banner content background'),
                'required' => false,
                'class' => "bgcontent",
            )
        );
    $banner_content_tcolor->setRenderer($colourpickerRenderer);


          $content_background->setRenderer($colourpickerRenderer);
		$content_opacity = $fieldset->addField(
            'content_opacity',
            'text',
            array(
                'name' => 'content_opacity',
                'label' => __('Banner content opacity'),
                'title' => __('Banner content opacity'),
                'required' => false,
			)
        );
		
	
		
		$navigation_arrow = $fieldset->addField(
            'navigation_arrow',
            'select',
            [
                'name' => 'navigation_arrow',
                'label' => __('Show Navigation Arrows'),
                'title' => __('Show Navigation Arrows'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getNavigation(),
			]
        );
		$navigation_style = $fieldset->addField(
            'navigation_style',
            'select',
            [
                'name' => 'navigation_style',
                'label' => __('Navigation Arrows Style'),
                'title' => __('Navigation Arrows Style'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getNavigationstyle(),
				'onchange' => 'notEmpty()',
				'after_element_html' => '<td id="navi_arrow" class="scope-label"><i class="cws" id="navigation_style_name"></i></td>',
				'note' => 'Applied only if "Show Navigation Arrows" field is set to "On hover" or "Never".',
			]
        );		
			
		
	
		$navigation_acolor =$fieldset->addField(
            'navigation_acolor',
            'text',
            array(
                'name' => 'navigation_acolor',
                'label' => __('Navigation Arrows Color'),
                'title' => __('Navigation Arrows Color'),
                'class' => 'navigation_acolor',
				'note' => 'Applied only if "Show Navigation Arrows" field is set to "On hover" or "Never".',
			)
        );
        $navigation_acolor->setRenderer($colourpickerRenderer);

        $pagination_bar = $fieldset->addField(
            'pagination_bar',
            'text',
            array(
                'name' => 'pagination_bar',
                'label' => __('Navigation Arrows Background Color'),
                'title' => __('Navigation Arrows Background Color'),
                'required' => false,
                'class' => "pagination_bar",
                'note' => 'Applied only if "Show Navigation Arrows" field is set to "On hover" or "Never".',
            )
        );
        $pagination_bar->setRenderer($colourpickerRenderer);
		
		$show_pagination = $fieldset->addField(
            'show_pagination',
            'select',
            [
                'name' => 'show_pagination',
                'label' => __('Show Pagination'),
                'title' => __('Show Pagination'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getNavigation(),
			]
        );
		$pagination_style = $fieldset->addField(
            'pagination_style',
            'select',
            [
                'name' => 'pagination_style',
                'label' => __('Pagination Style'),
                'title' => __('Pagination Style'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getPaginationstyle(),
				'note' => 'Applied only if "Show Pagination" field is set to "On hover" or "Never".',
			]
        );
		
		$pagination_position = $fieldset->addField(
            'pagination_position',
            'select',
            [
                'name' => 'pagination_position',
                'label' => __('Pagination Position'),
                'title' => __('Pagination Position'),
                'required' => false,
                'values' => $this->_responsivebannerslider->getPaginationposition(),
				'note' => 'Applied only if "Show Pagination" field is set to "On hover" or "Never".',
			]
        );

        $colourpickerRenderer = $this->getLayout()->createBlock(
        'Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Color'
    );
		$pagination_color = $fieldset->addField(
            'pagination_color',
            'text',
            array(
                'name' => 'pagination_color',
                'label' => __('Pagination  Color'),
                'title' => __('Pagination  Color'),
                'required' => false,
				'class' => "pagination_color",
				'note' => 'Applied only if "Show Pagination" field is set to "On hover" or "Never".',
			)
        );
         $pagination_color->setRenderer($colourpickerRenderer);

        $pagination_bocolor = $fieldset->addField(
            'pagination_bocolor',
            'text',
            array(
                'name' => 'pagination_bocolor',
                'label' => __('Pagination Border Color'),
                'title' => __('Pagination Border Color'),
                'required' => false,
                'class' => "pagination_bocolor",
                'note' => 'Applied only if "Show Pagination" field is set to "On hover" or "Never".',
            )
        );
         $pagination_bocolor->setRenderer($colourpickerRenderer);
		
		$pagination_active = $fieldset->addField(
            'pagination_active',
            'text',
            array(
                'name' => 'pagination_active',
                'label' => __('Pagination Active Color'),
                'title' => __('Pagination Active Color'),
                'required' => false,
				'class' => "pagination_active",
				'note' => 'Applied only if "Show Pagination" field is set to "On hover" or "Never".',
			)
        );
        $pagination_active->setRenderer($colourpickerRenderer);
		
		
	
		$store_model = $this->_store->getCollection()->addFieldToFilter('slidergroup_id',array('eq' => $model->getId()));
		$model_data = $model->getData();
		$store_data = array();
		foreach($store_model as $s_data){
			$store_data[] = $s_data->getData('store_id');
		}
		array_push($model_data,$model_data['store_id'] = $store_data);
		
        $form->setValues($model_data);
        $this->setForm($form);
		
		if($model->getId()== ""){
			$model_data['slide_duration'] = '10000';
            $model_data['lazy_load'] = '1';
            $model_data['loop_slider'] = '1';
            $model_data['navigation_arrow'] = 'never'; 
             $model_data['show_pagination'] = 'always';  
            $model_data['pagination_style'] = 'numbers';  
            $model_data['slides_per_view'] = '1';
			$model_data['content_background'] = '';
			$model_data['content_opacity'] = '9';
            $model_data['banner_content_tcolor'] = '#000000';
			$model_data['navigation_acolor'] = '#0f0f0f';
			$model_data['pagination_bocolor'] = '#ffffff';
			$model_data['pagination_active'] = '#ffffff';
			$model_data['pagination_bar'] = 'rgba(255, 255, 255, 0.5)';
            $model_data['space_between'] = '0';
			$form->setValues($model_data);
			$this->setForm($form);
			
		}
		
		
		
        return parent::_prepareForm();   
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
