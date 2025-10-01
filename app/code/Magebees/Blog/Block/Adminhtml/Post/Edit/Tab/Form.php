<?php 
namespace  Magebees\Blog\Block\Adminhtml\Post\Edit\Tab;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{		
		protected $_helper;
		protected $_wysiwygConfig;
		protected $_customergroup;
		protected $_systemStore;
		protected $_theme;
		protected $_pagelayout;
		protected $storeManager;
		protected $filesystem;
		protected $_rendererFieldset;
		protected $authSession;
		protected $timezone;
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
		StoreManagerInterface $storeManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
		\Magento\Backend\Model\Auth\Session $authSession, 
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		array $data = []
    ) {
        $this->_helper = $helper;
		$this->_wysiwygConfig = $wysiwygConfig;
		 $this->_customergroup = $customergroup;
		 $this->_systemStore = $systemStore;
		 $this->_theme = $theme;
		 $this->_pagelayout = $pagelayout;
		 $this->storeManager = $storeManager;
		 $this->filesystem = $filesystem;
		 $this->_rendererFieldset = $rendererFieldset;
		 $this->authSession = $authSession;
		$this->timezone = $timezone;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
    protected function _prepareForm()
    {
		
		
        $model = $this->_coreRegistry->registry('post');
		
        $title=$model->getTitle();
        $isElementDisabled = false;
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        if ($model->getPostId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Post "'.$title.'" Information')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Post')]);
        }
            
		$data = null;
		$current_id = null;
        if ($model->getPostId()) {
			$current_id = $model->getPostId();
            $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
			$fieldset->addField('creation_time', 'hidden', ['name' => 'creation_time']);
			$fieldset->addField('update_time', 'hidden', ['name' => 'update_time']);
			$fieldset->addField('current_identifier', 'hidden', ['name' => 'current_identifier']);
			
			$data = $model->getData();
			$data['post_id'] = $model->getPostId();
			$data['creation_time'] = $model->getCreationTime();
			$data['current_identifier'] = $model->getIdentifier();
			$data['media_gallery'] = $model->getMediaGallery();
			$data['featured_img'] = $model->getFeaturedImg();
			
		}else{
			$data['author_id'] = $this->authSession->getUser()->getUserId();
			$data['publish_time'] = $this->timezone->date()->format('Y-m-d H:i:s'); 
			$data['creation_time'] =  $this->timezone->date()->format('Y-m-d H:i:s'); 
			$data['update_time'] =  $this->timezone->date()->format('Y-m-d H:i:s'); 
		}
		
		
        /* Get object of helper using object manager*/
        // is_active 
		$fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Enable Post'),
                'title' => __('Enable Post'),
                'required' => true,
                'values' =>$this->_helper->getEnableDisableOptionArray()
            ]
        );
		
		
		
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Post Title'),
                'title' => __('Post Title'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		
		
		$fieldset = $form->addFieldset('post_gallery', ['legend' =>__('Images')]);
		$fieldset->addField('media_gallery', 'hidden', ['name' => 'media_gallery']);
		$fieldset->addField('featured_img', 'hidden', ['name' => 'featured_img']);
		 $fieldset->addField(
            'featuredImage', 
            'text', 
            [
                'name' => 'featuredImage',
                'label' => __('Featured Image'),
                'title' => __('Featured Image'),
                'required' => true,
                'disabled' => $isElementDisabled                  
            ]
        )->setRenderer($this->_rendererFieldset->setTemplate('Magebees_Blog::postfeatureimage.phtml'));  
		
		
		
		/* Code Start For Set Custom Div In the form*/
         /* $fieldset->addType(
            'mapping_content_blog',
            '\Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\MappingContent'
        );
        $fieldset->addField(
            'content_blog',
            'mapping_content_blog',
            [
            'name'  => 'content_blog',
           
            ]
        ); */
    /* Code End For Set Custom Div In the form*/
	/* Code For Add Phtml Page in which js code added */
      /*   $this->setChild(
            'content_blog',
            $this->getLayout()->createBlock(
                'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Content',
                'mapping.content'
            )
        );  */
    /* Code For Add Phtml Page in which js code compelted*/
		
		
		$fieldset = $form->addFieldset('post_content', ['legend' =>__('Content')]);
		$fieldset->addField('content', 'editor', [
			  'name'      => 'content',
			  'label' 	  => 'Content',
			  'config'    => $this->_wysiwygConfig->getConfig(),
			  'wysiwyg'   => true,
			'wysiwyg_enabled' =>	true,
			  'required'  => false
		]);
		
		$fieldset = $form->addFieldset('heading_content', ['legend' =>__('Short Content')]);
		$fieldset->addField('content_heading', 'editor', [
			  'name'      => 'content_heading',
			  'label' 	  => 'Short Content',
			  'config'    => $this->_wysiwygConfig->getConfig(),
			  'wysiwyg'   => true,
			'wysiwyg_enabled' =>	true,
			  'required'  => false
		]);
		
		
		
		$fieldset = $form->addFieldset('post_display', ['legend' =>__('Post Display Settings')]);
		
		$fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled,
            ]
        );
		$save_as_draft = $fieldset->addField(
            'save_as_draft',
            'select',
            [
                'name' => 'save_as_draft',
                'label' => __('Save As Draft'),
                'title' => __('Save As Draft'),
                'required' => true,
                'values' =>$this->_helper->getyesnoOptionArray()
            ]
        );	
		
		$publish_time = $fieldset->addField(
            'publish_time',
            'date',
            [
                'name' => 'publish_time',
                'label' => __('Publish Date'),
                'title' => __('Publish Date'),
				 'date_format' => 'yyyy-MM-dd ',
				'time_format' => 'HH:mm:ss',
				'disabled' => $isElementDisabled,
            ]
        );
		
			$fieldset->addField(
            'is_featured',
            'select',
            [
                'name' => 'is_featured',
                'label' => __('Is Featured'),
                'title' => __('Is Featured'),
                'required' => true,
                'values' =>$this->_helper->getyesnoOptionArray()
            ]
        );
		$fieldset->addField(
            'is_recent_posts_skip',
            'select',
            [
                'name' => 'is_recent_posts_skip',
                'label' => __('Include in Recent Posts'),
                'title' => __('Include in Recent Posts'),
                'required' => true,
                'values' =>$this->_helper->getyesnoOptionArray()
            ]
        );	
		$fieldset->addField(
            'author_id',
            'select',
            [
                'name' => 'author_id',
                'label' => __('Author'),
                'title' => __('Author'),
                'required' => true,
                'values' =>$this->_helper->getAdminUsers()
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
		
		$fieldset = $form->addFieldset('post_website', ['legend' =>__('Post in Websites')]);
		$fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
					 'renderer'=>  'Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Store',
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            ); 
		$fieldset = $form->addFieldset('post_seo', ['legend' =>__('Search Engine Optimization')]);
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
		
		$this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($save_as_draft->getHtmlId(), $save_as_draft->getName())
            ->addFieldMap($publish_time->getHtmlId(), $publish_time->getName())
            ->addFieldDependence(
                $publish_time->getName(),
                $save_as_draft->getName(),
                '0'
            ));
        
        return parent::_prepareForm();
    }

   
    public function getTabLabel()
    {
        return __('Blog Post');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Blog Post');
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
    
}
