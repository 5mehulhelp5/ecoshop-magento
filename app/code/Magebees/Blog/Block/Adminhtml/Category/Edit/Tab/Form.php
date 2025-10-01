<?php 
namespace  Magebees\Blog\Block\Adminhtml\Category\Edit\Tab;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{	
	
	protected $_helper;
    protected $_wysiwygConfig;
    protected $_customergroup;
    protected $_systemStore;
    protected $_theme;
    protected $_pagelayout;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
       	\Magebees\Blog\Helper\Data $helper,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, 
		\Magento\Customer\Model\ResourceModel\Group\Collection $customergroup,
		\Magento\Store\Model\System\Store $systemStore,
		\Magento\Cms\Model\Page\Source\Theme $theme,
		\Magento\Cms\Model\Page\Source\PageLayout $pagelayout,
		array $data = []
    ) {
        $this->_helper = $helper;
		$this->_wysiwygConfig = $wysiwygConfig;
		 $this->_customergroup = $customergroup;
		 $this->_systemStore = $systemStore;
		 $this->_theme = $theme;
		 $this->_pagelayout = $pagelayout;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('category');

        $title=$model->getTitle();
        $isElementDisabled = false;
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        if ($model->getCategoryId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Category "'.$title.'" Information')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Category')]);
        }
            
		$data = null;
		$current_id = null;
        if ($model->getCategoryId()) {
			$current_id = $model->getCategoryId();
            $data = $model->getData();
			$fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
			$fieldset->addField('current_identifier', 'hidden', ['name' => 'current_identifier']);
			$data['current_identifier'] = $model->getIdentifier();
			
        }else{
		$data['position'] = 0;
		$data['include_in_menu'] = 1;
		}
        
        /* Get object of helper using object manager*/
        // is_active 
		$fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Enable Category'),
                'title' => __('Enable Category'),
                'required' => true,
                'values' =>$this->_helper->getEnableDisableOptionArray()
            ]
        );
		
		
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		
		$fieldset->addField(
            'parent_category_id',
            'select',
            [
                'name' => 'parent_category_id',
                'label' => __('Parent Category'),
                'title' => __('Parent Category'),
                'required' => true,
                'values' =>$this->_helper->getParentOptionArray($current_id)
            ]
        );
		$fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'include_in_menu',
            'select',
            [
                'name' => 'include_in_menu',
                'label' => __('Include In Menu'),
                'title' => __('Include In Menu'),
                'required' => true,
                'values' =>$this->_helper->getyesnoOptionArray()
            ]
        );
		$fieldset = $form->addFieldset('category_content', ['legend' =>__('Content')]);
		$fieldset->addField('content', 'editor', [
			  'name'      => 'content',
			  'label' 	  => 'Content',
			  'config'    => $this->_wysiwygConfig->getConfig(),
			  'wysiwyg'   => true,
			  'required'  => false
		]);
		$fieldset = $form->addFieldset('category_display', ['legend' =>__('Category Display Settings')]);
		$fieldset->addField(
            'display_mode',
            'select',
            [
                'name' => 'display_mode',
                'label' => __('Display Mode'),
                'title' => __('Display Mode'),
                'required' => true,
                'values' =>$this->_helper->getdisplaymodeOptionArray()
            ]
        );
		$fieldset->addField(
            'posts_sort_by',
            'select',
            [
                'name' => 'posts_sort_by',
                'label' => __('Post Sort By'),
                'title' => __('Post Sort By'),
                'required' => true,
                'values' =>$this->_helper->getpostsortbyOptionArray()
            ]
        );
		$fieldset->addField(
			'customer_group',
			'multiselect',
			[
				'name' => 'customer_group[]',
				'label' => __('Customer Group'),
				'title' => __('Customer Group'),
				'required' => true,
				'values' => $this->_customergroup->toOptionArray(),
				'disabled' => $isElementDisabled
			]
		);
		$fieldset = $form->addFieldset('category_website', ['legend' =>__('Category in Websites')]);
		$fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
					 'renderer'=>  'Magebees\Blog\Block\Adminhtml\Category\Edit\Tab\Renderer\Store',
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            ); 
		$fieldset = $form->addFieldset('category_seo', ['legend' =>__('Search Engine Optimization')]);
		$fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('Meta Title'),
                'title' => __('Meta Title'),
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'meta_keywords',
            'text',
            [
                'name' => 'meta_keywords',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords'),
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'disabled' => $isElementDisabled,
            ]
        );
       	$form->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    
    public function getTabLabel()
    {
        return __('Blog Category');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Blog Category');
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
    public function isJSON($string)
    {
            return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
