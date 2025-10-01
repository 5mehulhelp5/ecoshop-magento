<?php 
namespace  Magebees\Blog\Block\Adminhtml\Urlrewrite\Edit\Tab;
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
        $model = $this->_coreRegistry->registry('urlrewrite');
	 	
        $title=$model->getOldUrl();
        $isElementDisabled = false;
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        if ($model->getUrlId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit UrlRewrite "'.$title.'" Information')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add UrlRewrite')]);
        }
            
		$data = null;
		$current_id = null;
        if ($model->getUrlId()) {
			$current_id = $model->getUrlId();
            $fieldset->addField('url_id', 'hidden', ['name' => 'url_id']);
			$data = $model->getData();
        }
        
       
        $fieldset->addField(
            'old_url',
            'text',
            [
                'name' => 'old_url',
                'label' => __('Old Url'),
                'title' => __('Old Url'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		 $fieldset->addField(
            'new_url',
            'text',
            [
                'name' => 'new_url',
                'label' => __('New Url'),
                'title' => __('New Url'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
		$fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Url Type'),
                'title' => __('Url Type'),
                'required' => true,
                'values' =>$this->_helper->getRewriteUrlType()
            ]
        );
		
       	$form->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    
    public function getTabLabel()
    {
        return __('Blog Url Rewrite');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
       return __('Blog Url Rewrite');
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
