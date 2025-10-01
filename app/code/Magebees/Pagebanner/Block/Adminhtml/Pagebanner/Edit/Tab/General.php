<?php
namespace Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
	protected $_categorytree;
	protected $categoryFlatConfig;
	protected $_widgetFactory;
	protected $pageCollectionFactory;
	protected $blogCategoryFactory;
       
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Catalog\Model\ResourceModel\Category\Tree $categorytree,
        \Magento\Catalog\Model\Category $categoryFlatState,
		\Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
		\Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
		\Magebees\Blog\Model\CategoryFactory $blogCategoryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->_categorytree = $categorytree;
        $this->categoryFlatConfig = $categoryFlatState;
		$this->_widgetFactory = $widgetFactory;
		$this->pageCollectionFactory = $pageCollectionFactory;
		$this->blogCategoryFactory = $blogCategoryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('pagebanner_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }
		$pageCollection = $this->pageCollectionFactory->create();
		$blogCategoryCollection = $this->blogCategoryFactory->create()->getCollection();
		$blogCategoryList = array();
		$blogCategoryList[''] = __('-- Please Select --');
		foreach($blogCategoryCollection as $blogCategory):
			$blogCategoryList[$blogCategory->getCategoryId()] = $blogCategory->getTitle();
		endforeach;
		$PageList = array();
		$PageList[''] = __('-- Please Select --');
		foreach($pageCollection as $page):
		$PageList[$page->getIdentifier()] = $page->getTitle();
		endforeach;
		
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
				'required' => true,
                'values' => [
                    '1' => __('Enabled'),
                    '0' => __('Disabled'),
                ],
            ]
        );
		
		$page_type_options = $fieldset->addField(
            'page_type_options',
            'select',
            [
                'name' => 'page_type_options',
                'label' => __('Page Type Options'),
                'title' => __('Page Type Options'),
				'required' => true,
				'onchange' => 'pageoptions(this.value);',
				'onload' => 'pageoptions(this.value);',
				'values' => [
                    '' => __('--Please Select--'),
					'cmspage' => __('CMS Page'),
                    'catalogcategory' => __('Catalog Category'),
                    'blogcategory' => __('Blog Category'),
                    'specifiedpage' => __('Specified Page'),
                ],
            ]
        );
        $cms_page_list_options = $fieldset->addField(
            'cms_page',
            'select',
            [
                'name' => 'cms_page',
                'label' => __('CMS Page'),
                'title' => __('CMS Page'),
				'required' => true,
                'values' => $PageList,
            ]
        );
		//categories field
		$categories_arr = [];
        $categories_arr = explode(",", (string)$model->getCategoryIds());
        
        $category = $fieldset->addField(
            'catalog_category',
            'select',
            [
                'name' => 'catalog_category',
                'label' => __('Category'),
                'title' => __('Category'),
                'required' => true,
                'values' => $this->toOptionArray(),
                'value'         => $categories_arr,
            ]
        );
		
		$blog_catgory = $fieldset->addField(
            'blog_category',
            'select',
            [
                'name' => 'blog_category',
                'label' => __('Blog Category'),
                'title' => __('Blog Category'),
				'required' => true,
                'values' => $blogCategoryList,
            ]
        );
		$fieldset->addType(
            'custom_page_type',
            '\Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Edit\Renderer\CustomRenderer'
        );
		$specified_page_options = $fieldset->addField(
        'specified_page_layout_handle',
        'custom_page_type',
        [
            'name'  => 'specified_page_layout_handle',
            'label' => __('Specified Page'),
            'title' => __('Specified Page'),
          
        ]
    );
	
		/*$chooserBlock->toHtml();*/
		if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'select',
                [
                'name' => 'stores',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, false),
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
       
        
        
		
		
		
		//Date Range for new product
        //$fieldset = $form->addFieldset('date_range_form', ['legend'=>__('Date Range')]);
        
        
		$dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
		$fielName = null;
		$fieldset->addField(
		'banner_image',
		'image',
		 [
			'name' => 'banner_image',
			'label' => __('Banner Image'),
			'title' => __('Banner Image'),
			'class' => 'banner_image',
			'data-form-part' => $this->getData('target_form'),
			'note' => __('Allowed image types: jpg,png,webp')
		  ]
		)->setAfterElementHtml('
        <script>
            require([
                 "jquery",
            ], function($){
                $(document).ready(function () {
                    if($("#banner_image").attr("value")){
                        $("#banner_image").removeClass("required-file");
                    }else{
                        $("#banner_image").addClass("required-file");
                    }
                    $( "#banner_image" ).attr( "accept", "image/x-png,image/gif,image/jpeg,image/jpg,image/png" );
                });
              });
       </script>
    ');
		
		$this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            
			->addFieldMap($page_type_options->getHtmlId(), $page_type_options->getName())
			->addFieldMap($cms_page_list_options->getHtmlId(), $cms_page_list_options->getName())
			->addFieldMap($blog_catgory->getHtmlId(), $blog_catgory->getName())
			->addFieldMap($category->getHtmlId(), $category->getName())
			->addFieldMap($specified_page_options->getHtmlId(), $specified_page_options->getName())
			
			->addFieldDependence(
                $cms_page_list_options->getName(),
				$page_type_options->getName(),
                'cmspage'
            )
			->addFieldDependence(
                $category->getName(),
				$page_type_options->getName(),
                'catalogcategory'
            )
			->addFieldDependence(
                $blog_catgory->getName(),
				$page_type_options->getName(),
                'blogcategory'
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
			if(isset($model_data['layout_handle']))
			{
				$model_data['specified_page_layout_handle'] = $model_data['layout_handle'];
			}
			if($model_data['banner_image'] != "") {
				$imgpath = "pagebanner".$model_data['banner_image'];
				array_push($model_data,$model_data['banner_image'] = $imgpath);
			}
			//specified_page_layout_handle
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
