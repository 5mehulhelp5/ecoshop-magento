<?php 
namespace  Magebees\Blog\Block\Adminhtml\Tag\Edit\Tab;
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
        $model = $this->_coreRegistry->registry('tag');
		
        $title=$model->getTitle();
        $isElementDisabled = false;
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        if ($model->getCategoryId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Tag "'.$title.'" Information')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Tag')]);
        }
            
		$data = null;
		$current_id = null;
        if ($model->getTagId()) {
			$current_id = $model->getTagId();
            $fieldset->addField('tag_id', 'hidden', ['name' => 'tag_id']);
			$fieldset->addField('current_identifier', 'hidden', ['name' => 'current_identifier']);
			$data = $model->getData();
			$data['current_identifier'] = $model->getIdentifier();
        }
        
        /* Get object of helper using object manager*/
        // is_active 
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
		
		
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Tag Title'),
                'title' => __('Tag Title'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		
		
		$fieldset = $form->addFieldset('tag_content', ['legend' =>__('Content')]);
		$fieldset->addField('content', 'editor', [
			  'name'      => 'content',
			  'label' 	  => 'Content',
			  'config'    => $this->_wysiwygConfig->getConfig(),
			  'wysiwyg'   => true,
			  'required'  => false
		]);
		
		
		
		
		
		$fieldset = $form->addFieldset('tag_seo', ['legend' =>__('Search Engine Optimization')]);
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
		$fieldset->addField(
                'meta_robots',
                'select',
                [
                    'name' => 'meta_robots',
                    'label' => __('Meta Robots'),
                    'title' => __('Meta Robots'),
                    'required' => true,
                    'values' => $this->_helper->getMetaRobotsOptionArray()
                ]
            );
		
       	$form->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    
    public function getTabLabel()
    {
        return __('Blog Tag');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Blog Tag');
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
