<?php
namespace Magebees\Blog\Block\Adminhtml\Import\Edit;
class Form extends \Magento\Backend\Block\Widget\Form\Generic
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
      
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    //'action' => $this->getUrl('*/*/save'),
                    //'method' => 'post',
                    //'enctype' => 'multipart/form-data'
                ]
            ]
        );
	   
		$isElementDisabled = false;
        $form->setHtmlIdPrefix('import_');
		$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Import Blogs')]);
        
            
	  
		$data = null;
	   
		$data['host'] = 'localhost';
	   $data['prefix'] = 'wp_';
	   
			   
		$current_id = null;
        
        /* Get object of helper using object manager*/
        // is_active 
		/*
		$fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Enable Tag'),
                'title' => __('Enable Tag'),
                'required' => true,
                'values' =>$this->_helper->getEnableDisableOptionArray()
            ]
        );
		*/
		
	    $fieldset->addField(
            'notice',
            'label',
            [
                'label' => __('NOTICE'),
                'name' => 'prefix',
                'after_element_html' => 'When the import is completed successfully, please copy image files from WordPress <strong style="color:#bd1616;">wp-content/uploads</strong> directory to Magento <strong style="color:#105610;">pub/media/magebees_blog</strong> directory.',
            ]
        );
        $fieldset->addField(
            'dbname',
            'text',
            [
                'name' => 'dbname',
                'label' => __('Database Name'),
                'title' => __('Database Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'username',
            'text',
            [
                'name' => 'username',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
	   $fieldset->addField(
            'password',
            'text',
            [
                'name' => 'password',
                'label' => __('Password'),
                'title' => __('Password'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
	   $fieldset->addField(
            'host',
            'text',
            [
                'name' => 'host',
                'label' => __('Database Host'),
                'title' => __('Database Host'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		 
	   $fieldset->addField(
            'prefix',
            'text',
            [
                'name' => 'prefix',
                'label' => __('Table Prefix'),
                'title' => __('Table Prefix'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
	  
		//   Store View
	   
	   $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
					 'renderer'=>  'Magebees\Blog\Block\Adminhtml\Import\Edit\Tab\Renderer\Store',
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            ); 
	   
	   
		$form->setUseContainer(true);
	   	$form->setValues($data);
        $this->setForm($form);
       // return parent::_prepareForm();
    }
}
