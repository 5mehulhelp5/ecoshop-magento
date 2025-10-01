<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab;

class Display extends \Magento\Backend\Block\Widget\Form\Generic
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
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Display Settings')]);
	
		$display_heading = $fieldset->addField(
            'display_heading',
            'select',
            [
                'label'     => __('Display heading'),
                'name'      => 'display_settings[display_heading]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
        

        $heading = $fieldset->addField(
            'heading',
            'text',
            [
                'name' => 'display_settings[heading]',
                'label' => __('Heading'),
                'title' => __('Heading'),
                'required' => true,
            ]
        );
        
        $display_desc = $fieldset->addField(
            'display_desc',
            'select',
            [
                'label'     => __('Display Short Description'),
                'name'      => 'display_settings[display_desc]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$short_desc = $fieldset->addField(
            'short_desc',
            'text',
            [
                'name' => 'display_settings[short_desc]',
                'label' => __('Short Description'),
                'title' => __('Short Description'),
                'required' => true,
            ]
        );
		
		$sort_by = $fieldset->addField(
            'sort_by',
            'select',
            [
                'label'     => __('Sort By'),
                'name'      => 'display_settings[sort_by]',
                'values'    => [
                    'position' => __('Position'),
                    'price' => __('Price'),
                    'name' => __('Name'),
					'random' => __('Random'),					
                ],
            ]
        );
		
		$sort_order = $fieldset->addField(
            'sort_order',
            'select',
            [
                'label'     => __('Sort Order'),
                'name'      => 'display_settings[sort_order]',
                'values'    => [
                    'ASC' => __('Ascending'),
                    'DESC' => __('Descending'),
                ],
            ]
        );
		
		$fieldset->addField(
            'display_price',
            'select',
            [
                'label'     => __('Display Product Price'),
                'name'      => 'display_settings[display_price]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$product_short_description = $fieldset->addField(
            'display_product_short_description',
            'select',
            [
                'label'     => __('Display Product Short Description'),
                'name'      => 'display_settings[display_product_short_description]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$product_short_description_lengh = $fieldset->addField( 
			'product_short_description_length',          
			'text',            [
			'label'     => __('Product Short Description Length'),
			'name'      => 'display_settings[product_short_description_length]', 
			'required' => true,
			]        
		);
		$fieldset->addField(
            'display_addtocart',
            'select',
            [
                'label'     => __('Display Add to Cart'),
                'name'      => 'display_settings[display_addtocart]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$fieldset->addField(
            'display_addtocompare',
            'select',
            [
                'label'     => __('Display Add to Compare'),
                'name'      => 'display_settings[display_addtocompare]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$fieldset->addField(
            'display_addtowishlist',
            'select',
            [
                'label'     => __('Display Add to Wishlist'),
                'name'      => 'display_settings[display_addtowishlist]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$fieldset->addField(
            'display_outofstock',
            'select',
            [
                'label'     => __('Display Out of Stock Product'),
                'name'      => 'display_settings[display_outofstock]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		$fieldset->addField( 
			'custom_class',          
			'text',            [
			'label'     => __('Custom Class'),
			'name'      => 'display_settings[custom_class]', 
			]        
		);
		
		$this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($display_heading->getHtmlId(), $display_heading->getName())
            ->addFieldMap($heading->getHtmlId(), $heading->getName())
            ->addFieldMap($display_desc->getHtmlId(), $display_desc->getName())
            ->addFieldMap($short_desc->getHtmlId(), $short_desc->getName())
			->addFieldMap($product_short_description->getHtmlId(), $product_short_description->getName())
			->addFieldMap($product_short_description_lengh->getHtmlId(), $product_short_description_lengh->getName())
            ->addFieldDependence(
                $heading->getName(),
				$display_heading->getName(),
                1
            )
			->addFieldDependence(
                
                $short_desc->getName(),
				$display_desc->getName(),
                1
            )
			->addFieldDependence(
                $product_short_description_lengh->getName(),
				$product_short_description->getName(),
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
