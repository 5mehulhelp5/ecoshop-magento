<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab;

class LayoutOptions extends \Magento\Backend\Block\Widget\Form\Generic
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('todaydeal_data');
          
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('layout_fieldset', ['legend' => __('Layout Options')]);
            
        $fieldset->addField(
            'price',
            'select',
            [
                'name' => 'layout[price]',
                'label' => __('Display Product\'s Price'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
            
        $fieldset->addField(
            'cart',
            'select',
            [
                'name' => 'layout[cart]',
                'label' => __('Display Add to Cart'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        
        $fieldset->addField(
            'compare',
            'select',
            [
                'name' => 'layout[compare]',
                'label' => __('Display Add to Compare'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        
        $fieldset->addField(
            'wishlist',
            'select',
            [
                'name' => 'layout[wishlist]',
                'label' => __('Display Add to Wishlist'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        
        $fieldset->addField(
            'out_of_stock',
            'select',
            [
                'name' => 'layout[out_of_stock]',
                'label' => __('Display Out of Stock'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        
		$fieldset->addField(
            'total_products',
            'text',
            [
                'name' => 'layout[total_products]',
                'label' => __('Set Total Number of Products for display'),
                'note' => __('Set 0 or remain blank field for display all products.'),
            ]
        );
		
        $pager = $fieldset->addField(
            'pager',
            'select',
            [
                'name' => 'layout[pager]',
                'label' => __('Display Pager'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        
        $products_per_page = $fieldset->addField(
            'products_per_page',
            'text',
            [
                'name' => 'layout[products_per_page]',
                'label' => __('Set Number of Products per Page'),
                'note' => __('Set value greater than 0. Default products per page is 5.')
            ]
        );
        
        
        
        $enable_slider = $fieldset->addField(
            'enable_slider',
            'select',
            [
                'name' => 'layout[enable_slider]',
                'label' => __('Enable Slider'),
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
            ]
        );
        //SLider yes
		$items_per_slide = $fieldset->addField(
            'items_per_slide',
            'select',
            [
                'name' => 'layout[items_per_slide]',
                'label' => __('Number of items per slide'),
                'title' => __('Number of items per slide'),
                'required' => true,
                //'class' => 'validate-digits',
				'values'    => [
                    2 => __(2),
                    3 => __(3),
                    4 => __(4),
                    5 => __(5),
                ],
                //'note' => __('Add font size in pixels'),
            ]
        );
		
		$autoplay = $fieldset->addField(
            'autoplay',
            'select',
            [
                'label'     => __('Autoplay'),
                'name'      => 'layout[autoplay]',
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
                'name' => 'layout[delay_time]',
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
                'name'      => 'layout[mouse_enter]',
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
                'name'      => 'layout[auto_height]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$nav_arr = $fieldset->addField(
            'nav_arr',
            'select',
            [
                'label'     => __('Show navigation arrow'),
                'name'      => 'layout[nav_arr]',
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
                'name'      => 'layout[pagination]',
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
                'name'      => 'layout[pagi_type]',
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
                'name'      => 'layout[loop]',
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
                'name'      => 'layout[scrollbar]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$grab_cur = $fieldset->addField(
            'grab_cur',
            'select',
            [
                'label'     => __('Slide Grab cursor'),
                'name'      => 'layout[grab_cur]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		
            
        /*$model_data = $model->getData();
        $form->setValues($model_data);
        $this->setForm($form); */
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $informations = $model->getData();
            foreach ($informations as $key => $value) :
                if ($this->isJSON($value)) {
                    $sub_information = json_decode($value, true);
                    foreach ($sub_information as $subkey => $subvalue) :
                        $informations[$subkey] = $subvalue;
                    endforeach;
                } else {
                    $informations[$key] = $value;
                }
            endforeach;
            $form->setValues($informations);
        }
        
        $this->setForm($form);
		
	  	$this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
		->addFieldMap($pager->getHtmlId(), $pager->getName())
		->addFieldMap($products_per_page->getHtmlId(), $products_per_page->getName())
		->addFieldMap($enable_slider->getHtmlId(), $enable_slider->getName())
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
		->addFieldMap($grab_cur->getHtmlId(), $grab_cur->getName())
		->addFieldDependence(
			$products_per_page->getName(),
			$pager->getName(),
			1
		)
		->addFieldDependence(
			$enable_slider->getName(),
			$pager->getName(),
			0
		)
		
		->addFieldDependence(
			$items_per_slide->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$autoplay->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$delay_time->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$mouse_enter->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$auto_height->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$nav_arr->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$pagination->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$pagi_type->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$loop->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$scrollbar->getName(),
			$pager->getName(),
			0
		)
		->addFieldDependence(
			$grab_cur->getName(),
			$pager->getName(),
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
		
			
		);
		
            
        return parent::_prepareForm();
    }

    public function isJSON($string)
    {
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
