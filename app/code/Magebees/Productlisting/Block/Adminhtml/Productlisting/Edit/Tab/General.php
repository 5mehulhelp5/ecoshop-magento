<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
       
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Catalog\Model\ResourceModel\Category\Tree $categorytree,
        \Magento\Catalog\Model\Category $categoryFlatState,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->_categorytree = $categorytree;
        $this->categoryFlatConfig = $categoryFlatState;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('productlisting_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('listing_id', 'hidden', ['name' => 'listing_id']);
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
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => [
                    '1' => __('Enabled'),
                    '0' => __('Disabled'),
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
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }		
       
        
        $product_type_options = $fieldset->addField(
            'product_type_options',
            'select',
            [
                'name' => 'general[product_type_options]',
                'label' => __('Product Type Options'),
                'title' => __('Product Type Options'),
                'values' => [
                    'featured' => __('Featured'),
                    'new' => __('New'),
                    'mostview' => __('Most Viewed'),
                    'bestseller' => __('Bestseller'),
                ],
            ]
        );
		
		$collection_type = $fieldset->addField(
            'collection_type',
            'select',
            [
                'name' => 'general[collection_type]',
                'label' => __('Collection Type'),
                'title' => __('Collection Type'),
                'values' => [
                    'auto' => __('Auto'),
                    'manually' => __('Manually'),
                    'both' => __('Both'),
                ],
            ]
        );
		
		$display_by = $fieldset->addField(
            'display_by',
            'select',
            [
                'name' => 'general[display_by]',
                'label' => __('Display By'),
                'title' => __('Display By'),
                'values' => [
                    'all' => __('All Products'),
                    'cat' => __('Category Wise'),
                ],
            ]
        );
		
		
		//categories field
		$categories_arr = [];
        $categories_arr = explode(",", (string)$model->getCategoryIds());
        
        $category_ids = $fieldset->addField(
            'category_ids',
            'multiselect',
            [
                'name' => 'category_ids[]',
                'label' => __('Visible In'),
                'title' => __('Visible In'),
                'required' => false,
                'values' => $this->toOptionArray(),
                'value'         => $categories_arr,
            ]
        );
		
		//Date Range for new product
        //$fieldset = $form->addFieldset('date_range_form', ['legend'=>__('Date Range')]);
        
        $date_enabled = $fieldset->addField(
            'date_enabled',
            'select',
            [
                'label'     => __('Use Date Range'),
                'name'      => 'general[date_enabled]',
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );
		
		$dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
       
        $new_from_date = $fieldset->addField(
            'new_from_date',
            'date',
            [
                'name' => 'general[new_from_date]',
                'label' => __('New from Date'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
        
        $new_to_date = $fieldset->addField(
            'new_to_date',
            'date',
            [
                'name' => 'general[new_to_date]',
                'label' => __('New To Date'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
		
		$new_threshold = $fieldset->addField(
            'new_threshold',
            'text',
            [
                'name' => 'general[new_threshold]',
                'label' => __('Product is new threshold'),
                'title' => __('Product is new threshold'),
                'required' => true,
            ]
        );
		
		//Bestseller >>
		
		$bundle_config = $fieldset->addField(
            'bundle_config',
            'select',
            [
                'name' => 'general[bundle_config]',
                'label' => __('Display Bundle And configurable Product'),
                'title' => __('Display Bundle And configurable Product'),
                'values' => [
                    'child' => __('Only Child'),
                    'parent' => __('Only Parent'),
                    'both' => __('Both'),
                ],
            ]
        );
		
		
		$best_time = $fieldset->addField(
            'best_time',
            'text',
            [
                'name' => 'general[best_time]',
                'label' => __('Time Period'),
                'title' => __('Time Period'),
                'required' => true,
            ]
        );
		
		$order_status = $fieldset->addField(
            'order_status',
            'select',
            [
                'name' => 'general[order_status]',
                'label' => __('Order Status'),
                'title' => __('Order Status'),
                'values' => [
                    'complete' => __('Complete'),
                    'processing' => __('Processing'),
                    'pending' => __('Pending'),
                    'all' => __('All'),
                ],
            ]
        );
		
		$template = $fieldset->addField(
            'template',
            'select',
            [
                'name' => 'general[template]',
                'label' => __('Select Template'),
                'title' => __('Select Template'),
                'values' => [
                    'grid' => __('Grid'),
                    'list' => __('List'),
                    'list_style1' => __('List Style1'),
					'sidebar' => __('Sidebar'),
                ],
            ]
        );
        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($product_type_options->getHtmlId(), $product_type_options->getName())
            ->addFieldMap($collection_type->getHtmlId(), $collection_type->getName())
            ->addFieldMap($display_by->getHtmlId(), $display_by->getName())
            ->addFieldMap($category_ids->getHtmlId(), $category_ids->getName())
            ->addFieldMap($date_enabled->getHtmlId(), $date_enabled->getName())
            ->addFieldMap($new_from_date->getHtmlId(), $new_from_date->getName())
            ->addFieldMap($new_to_date->getHtmlId(), $new_to_date->getName())
            ->addFieldMap($new_threshold->getHtmlId(), $new_threshold->getName())
            ->addFieldMap($bundle_config->getHtmlId(), $bundle_config->getName())
            ->addFieldMap($best_time->getHtmlId(), $best_time->getName())
            ->addFieldMap($order_status->getHtmlId(), $order_status->getName())
			->addFieldDependence(
                $category_ids->getName(),
				$display_by->getName(),
                'cat'
            )
			->addFieldDependence(
                $date_enabled->getName(),
				$product_type_options->getName(),
                'new'
            )
			->addFieldDependence(
                $new_from_date->getName(),
				$product_type_options->getName(),
                'new'
            )
			->addFieldDependence(
                $new_to_date->getName(),
				$product_type_options->getName(),
                'new'
            )
			->addFieldDependence(
                $new_threshold->getName(),
				$product_type_options->getName(),
                'new'
            )
			->addFieldDependence(
                $new_from_date->getName(),
				$date_enabled->getName(),
                1
            )
			->addFieldDependence(
                $new_to_date->getName(),
				$date_enabled->getName(),
                1
            )
			->addFieldDependence(
                $new_threshold->getName(),
				$date_enabled->getName(),
                0
            )
			->addFieldDependence(
                $bundle_config->getName(),
				$product_type_options->getName(),
                'bestseller'
            )
			->addFieldDependence(
                $best_time->getName(),
				$product_type_options->getName(),
                'bestseller'
            )
			->addFieldDependence(
                $order_status->getName(),
				$product_type_options->getName(),
                'bestseller'
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
	
	 public function buildCategoriesMultiselectValues($node, $values, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $level++;
        if ($level > 1) {
            $values[$node->getId()]['value'] = $node->getId();
            $values[$node->getId()]['label'] = str_repeat($nonEscapableNbspChar, ($level - 2) * 5).$node->getName();
        }

        foreach ($node->getChildren() as $child) {
            $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
        }

        return $values;
    }
	
	public function toOptionArray()
    {
        $tree = $this->_categorytree->load();
        $parentId = 1;
        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName('Root');
        }

        $collection = $this->categoryFlatConfig->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        $values['---'] = [
            'value' => '',
            'label' => '',
        ];
        return $this->buildCategoriesMultiselectValues($root, $values);
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
