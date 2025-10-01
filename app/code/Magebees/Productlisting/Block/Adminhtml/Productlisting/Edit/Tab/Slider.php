<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab;

class Slider extends \Magento\Backend\Block\Widget\Form\Generic
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
        
        $model = $this->_coreRegistry->registry('productlisting_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pagination and Slider Options')]);

        $fieldset->addField(
            'num_of_prod',
            'text',
            [
                'name' => 'slider_options[num_of_prod]',
                'label' => __('Number of Products'),
                'title' => __('Number of Products'),
                'required' => true,
                'class' => 'validate-digits',
                //'note' => __('Add font size in pixels'),
            ]
        );
        
		$enable_slider = $fieldset->addField(
            'enable_slider',
            'select',
            [
                'label'     => __('Enable Slider'),
                'name'      => 'slider_options[enable_slider]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		 
		
		$items_per_row = $fieldset->addField(
            'items_per_row',
            'select',
            [
                'name' => 'slider_options[items_per_row]',
                'label' => __('Number of items per row'),
                'title' => __('Number of items per row'),
                'required' => true,
                //'class' => 'validate-digits',
				'values'    => [
                    2 => __(2),
                    3 => __(3),
                    4 => __(4),
                    5 => __(5),
                ],
                'note' => __('Option applicable only on Grid Template'),
            ]
        );
		
		$items_per_page = $fieldset->addField(
            'items_per_page',
            'text',
            [
                'name' => 'slider_options[items_per_page]',
                'label' => __('Items per Page'),
                'title' => __('Items per Page'),
                'required' => true,
                'class' => 'validate-digits',
                //'note' => __('Add font size in pixels'),
            ]
        );
		
		
		$autoscroll = $fieldset->addField(
            'autoscroll',
            'select',
            [
                'label'     => __('Auto Load Next Page'),
                'name'      => 'slider_options[autoscroll]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
				'note' => __('Supported if Magebees Ajax Infinite Scroll enabled.'),
            ]
        );
		
		$loading_type = $fieldset->addField(
            'loading_type',
            'select',
            [
                'label'     => __('Loading type for load page content'),
                'name'      => 'slider_options[loading_type]',
                'values'    => [
                    0 => __('On Page Scroll'),
                    1 => __('On button click'),
                ],
				//'note' => __('Supported if Magebees Ajax Infinite Scroll enabled.'),
            ]
        );
		
		$text_no_more = $fieldset->addField(
            'text_no_more',
            'text',
            [
                'name' => 'slider_options[text_no_more]',
                'label' => __('No More Content Text'),
                'title' => __('No More Content Text'),
                'required' => true,
                //'class' => 'validate-digits',
                //'note' => __('Add font size in pixels'),
            ]
        );
		
		$threshold = $fieldset->addField(
            'threshold',
            'text',
            [
                'name' => 'slider_options[threshold]',
                'label' => __('Threshold'),
                'title' => __('Threshold'),
                'required' => true,
                'class' => 'validate-digits',
                //'note' => __('Add font size in pixels'),
            ]
        );
		
		$show_page_no = $fieldset->addField(
            'show_page_no',
            'select',
            [
                'label'     => __('Show Page Number'),
                'name'      => 'slider_options[show_page_no]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
			]
        );
		
		$label_prev_button = $fieldset->addField(
            'label_prev_button',
            'text',
            [
                'name' => 'slider_options[label_prev_button]',
                'label' => __('Label for the previous button'),
                'title' => __('Label for the previous button'),
                'required' => true,
            ]
        );
		
		$label_next_button = $fieldset->addField(
            'label_next_button',
            'text',
            [
                'name' => 'slider_options[label_next_button]',
                'label' => __('Label for the next button'),
                'title' => __('Label for the next button'),
                'required' => true,
            ]
        );
		
		$load_button_style = $fieldset->addField(
            'load_button_style',
            'text',
            [
                'name' => 'slider_options[load_button_style]',
                'label' => __('Loading Button Style'),
                'title' => __('Loading Button Style'),
                'required' => false,
				'note'	=> ('Type in your custom button style , e.g.: background-color:#000000;color:#ffffff;')

            ]
        );
		
		
		
		
        
		//SLider yes
		$items_per_slide = $fieldset->addField(
            'items_per_slide',
            'select',
            [
                'name' => 'slider_options[items_per_slide]',
                'label' => __('Number of items per slide'),
                'title' => __('Number of items per slide'),
                'required' => true,
                //'class' => 'validate-digits',
				'values'    => [
                    1 => __(1),
					2 => __(2),
                    3 => __(3),
                    4 => __(4),
                    5 => __(5),
					6 => __(6),
                ],
				'note' => __('Option applicable only on Grid Template'),
            ]
        );
		
		$autoplay = $fieldset->addField(
            'autoplay',
            'select',
            [
                'label'     => __('Autoplay'),
                'name'      => 'slider_options[autoplay]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		
		$delay_time = $fieldset->addField(
            'delay_time',
            'text',
            [
                'name' => 'slider_options[delay_time]',
                'label' => __('Delay Time'),
                'title' => __('Delay Time'),
                'required' => true,
                'class' => 'validate-digits',
                //'note' => __('Add font size in pixels'),
            ]
        );
		
		$mouse_enter = $fieldset->addField(
            'mouse_enter',
            'select',
            [
                'label'     => __('Disable On Mouse Enter'),
                'name'      => 'slider_options[mouse_enter]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$auto_height = $fieldset->addField(
            'auto_height',
            'select',
            [
                'label'     => __('Slide Auto Height'),
                'name'      => 'slider_options[auto_height]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
				'note' => __('Option applicable only on Grid Template'),
            ]
        );
		$nav_arr = $fieldset->addField(
            'nav_arr',
            'select',
            [
                'label'     => __('Show navigation arrow'),
                'name'      => 'slider_options[nav_arr]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$pagination = $fieldset->addField(
            'pagination',
            'select',
            [
                'label'     => __('Show Pagination'),
                'name'      => 'slider_options[pagination]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$pagi_type = $fieldset->addField(
            'pagi_type',
            'select',
            [
                'label'     => __('Pagination Type'),
                'name'      => 'slider_options[pagi_type]',
                'values'    => [
                    'default' => __('default'),
                    'dynamic' => __('dynamic'),
                    'progress' => __('progress'),
                    'fraction' => __('fraction'),
                    'custom' => __('custom'),
                ],
            ]
        );
		$loop = $fieldset->addField(
            'loop',
            'select',
            [
                'label'     => __('Infinite loop'),
                'name'      => 'slider_options[loop]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$scrollbar = $fieldset->addField(
            'scrollbar',
            'select',
            [
                'label'     => __('Show Scrollbar'),
                'name'      => 'slider_options[scrollbar]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
				'note' => __('Option applicable only on Grid Template'),
            ]
        );
		$grab_cur = $fieldset->addField(
            'grab_cur',
            'select',
            [				'label'     => __('Slide Grab cursor'),
                'name'      => 'slider_options[grab_cur]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );		$grid = $fieldset->addField(            'grid',            'select',            [				'label'     => __('Slide Grid'),                'name'      => 'slider_options[grid]',                'values'    => [                    0 => __('No'),                    1 => __('Yes'),                ],				'note' => __('Option applicable only on Grid Template'),            ]        );
		
		$view_more = $fieldset->addField(
            'view_more',
            'select',
            [
                'label'     => __('Show View More Button'),
                'name'      => 'slider_options[view_more]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$view_more_btn_txt = $fieldset->addField(
            'view_more_btn_txt',
            'text',
            [
                'name' => 'slider_options[view_more_btn_txt]',
                'label' => __('View More Button Title'),
                'title' => __('View More Button Title'),
                'required' => true,
            ]
        );
		
		$view_more_path = $fieldset->addField(
            'view_more_path',
            'text',
            [
                'name' => 'slider_options[view_more_path]',
                'label' => __('View More Button URL'),
                'title' => __('View More Button URL'),
                'required' => true,
            ]
        );
		
		
		$this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($enable_slider->getHtmlId(), $enable_slider->getName())
            ->addFieldMap($items_per_row->getHtmlId(), $items_per_row->getName())
            ->addFieldMap($items_per_page->getHtmlId(), $items_per_page->getName())
            ->addFieldMap($autoscroll->getHtmlId(), $autoscroll->getName())
            ->addFieldMap($loading_type->getHtmlId(), $loading_type->getName())
            ->addFieldMap($text_no_more->getHtmlId(), $text_no_more->getName())
            ->addFieldMap($threshold->getHtmlId(), $threshold->getName())
            ->addFieldMap($show_page_no->getHtmlId(), $show_page_no->getName())
            ->addFieldMap($label_prev_button->getHtmlId(), $label_prev_button->getName())
            ->addFieldMap($label_next_button->getHtmlId(), $label_next_button->getName())
            ->addFieldMap($load_button_style->getHtmlId(), $load_button_style->getName())
			->addFieldMap($items_per_slide->getHtmlId(), $items_per_slide->getName())
            ->addFieldMap($autoplay->getHtmlId(), $autoplay->getName())
            ->addFieldMap($delay_time->getHtmlId(), $delay_time->getName())
            ->addFieldMap($mouse_enter->getHtmlId(), $mouse_enter->getName())
            ->addFieldMap($auto_height->getHtmlId(), $auto_height->getName())
            ->addFieldMap($nav_arr->getHtmlId(), $nav_arr->getName())
            ->addFieldMap($pagination->getHtmlId(), $pagination->getName())
            ->addFieldMap($pagi_type->getHtmlId(), $pagi_type->getName())
            ->addFieldMap($loop->getHtmlId(), $loop->getName())
            ->addFieldMap($scrollbar->getHtmlId(), $scrollbar->getName())
            ->addFieldMap($grab_cur->getHtmlId(), $grab_cur->getName())			->addFieldMap($grid->getHtmlId(), $grid->getName())
			->addFieldMap($view_more->getHtmlId(), $view_more->getName())
			->addFieldMap($view_more_btn_txt->getHtmlId(), $view_more_btn_txt->getName())
			->addFieldMap($view_more_path->getHtmlId(), $view_more_path->getName())
			
			->addFieldDependence(
                $items_per_row->getName(),
                $enable_slider->getName(),
                0
            )
			->addFieldDependence(
                $items_per_page->getName(),
                $enable_slider->getName(),
                0
            )
			->addFieldDependence(
                $autoscroll->getName(),
                $enable_slider->getName(),
                0
            )						
			->addFieldDependence(                
				$loading_type->getName(),
                $enable_slider->getName(),
                0
			)
			->addFieldDependence(
				$text_no_more->getName(),
                $enable_slider->getName(),
                0            
			)
			->addFieldDependence(
				$threshold->getName(),
                $enable_slider->getName(),
                0
			)
			->addFieldDependence(
				$show_page_no->getName(),
                $enable_slider->getName(),
                0            
			)
			->addFieldDependence(
				$label_prev_button->getName(),
                $enable_slider->getName(),
                0            
			)
			->addFieldDependence(
				$label_next_button->getName(),
                $enable_slider->getName(),
                0
			)
			->addFieldDependence(
				$load_button_style->getName(),
                $enable_slider->getName(),
                0
			)
			
            ->addFieldDependence(
                $items_per_slide->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $autoplay->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $delay_time->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $mouse_enter->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $auto_height->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $nav_arr->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $pagination->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $pagi_type->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $loop->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $scrollbar->getName(),
                $enable_slider->getName(),
                1
            )
			->addFieldDependence(
                $grab_cur->getName(),
                $enable_slider->getName(),
                1
            )->addFieldDependence(                $grid->getName(),                $enable_slider->getName(),                1            )
			->addFieldDependence(
                $view_more->getName(),
                $autoscroll->getName(),
                0
            )
			->addFieldDependence(
                $view_more_btn_txt->getName(),
                $autoscroll->getName(),
                0
            )
			->addFieldDependence(
                $view_more_path->getName(),
                $autoscroll->getName(),
                0
            )
			->addFieldDependence(
                $view_more_btn_txt->getName(),
                $view_more->getName(),
                1
            )
			->addFieldDependence(
                $view_more_path->getName(),
                $view_more->getName(),
                1
            )
			->addFieldDependence(
                $delay_time->getName(),
                $autoplay->getName(),
                1
            )
			->addFieldDependence(
                $pagi_type->getName(),
                $pagination->getName(),
                1
            )
			->addFieldDependence(
                $loading_type->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $text_no_more->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $threshold->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $show_page_no->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $label_prev_button->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $label_next_button->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $load_button_style->getName(),
                $autoscroll->getName(),
                1
            )
			->addFieldDependence(
                $label_prev_button->getName(),
                $loading_type->getName(),
                1
            )
			->addFieldDependence(
                $label_next_button->getName(),
                $loading_type->getName(),
                1
            )
			->addFieldDependence(
                $load_button_style->getName(),
                $loading_type->getName(),
                1
            )
			
			
		
		);
        $model_data = $model->getData();
        if (!empty($model_data)) {
			foreach ($model_data as $key => $value){
                if ($this->isJSON($value)) {
                    $sub_information = json_decode($value, true);
					foreach ($sub_information as $subkey => $subvalue) {
						$model_data[$subkey] = $subvalue;
					}
				} else {
					$model_data[$key] = $value;
				}
			}	
            $form->setValues($model_data);
        }
		
        $this->setForm($form);
            
        return parent::_prepareForm();
    }
	
	public function isJSON($string){
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
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
