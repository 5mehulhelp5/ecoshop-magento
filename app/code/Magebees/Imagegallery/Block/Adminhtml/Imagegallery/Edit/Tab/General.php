<?php
namespace Magebees\Imagegallery\Block\Adminhtml\Imagegallery\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
    protected $_categorytree;
	protected $categoryFlatConfig;
	protected $_widgetFactory;
	protected $pageCollectionFactory;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Catalog\Model\ResourceModel\Category\Tree $categorytree,
        \Magento\Catalog\Model\Category $categoryFlatState,
		\Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
		\Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
		array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->_categorytree = $categorytree;
        $this->categoryFlatConfig = $categoryFlatState;
		$this->_widgetFactory = $widgetFactory;
		$this->pageCollectionFactory = $pageCollectionFactory;
		parent::__construct($context, $registry, $formFactory, $data);
    }
    
 
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('imagegallery_data');
          
        $form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('image_id', 'hidden', ['name' => 'image_id']);
        }
		$pageCollection = $this->pageCollectionFactory->create();
		
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
		$fieldset->addField(
            'isexternal',
			'select',
            [
                'name' => 'isexternal',
                'label' => __('Is External'),
                'title' => __('Is External'),
				'required' => true,
				'onchange' => 'urloptions(this.value);',
				'onload' => 'urloptions(this.value);',
                'values' => [
                    '0' => __('no'),
					'1' => __('Yes'),
                ],
            ]
        );
		$fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => true,
            ]
        );
		$fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Position'),
                'title' => __('Position')
            ]
        );
		/*$chooserBlock->toHtml();*/
		if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                'name' => 'stores',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(true, true)
				,
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
		'image',
		'image',
		 [
			'name' => 'image',
			'label' => __('Image'),
			'title' => __('Image'),
			'class' => 'image',
			'data-form-part' => $this->getData('target_form'),
			'note' => __('Allowed image types: jpg,png,webp <br/> Recommanded File Size should be 500 X 500.')
		  ]
		)->setAfterElementHtml('
        <script>
            require([
                 "jquery",
            ], function($){
                $(document).ready(function () {
                    if($("#image").attr("value")){
                        $("#image").removeClass("required-file");
						
                    }else{
                        $("#image").addClass("required-file");
                    }
                    $( "#image" ).attr( "accept", "image/x-png,image/gif,image/jpeg,image/jpg,image/png" );
                });
              });
       </script>
    ');
		
		
		
		$model_data = $model->getData();
        if (!empty($model_data)) {
			if($model_data['image'] != "") {
				$imgpath = "imagegallery".$model_data['image'];
				array_push($model_data,$model_data['image'] = $imgpath);
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
