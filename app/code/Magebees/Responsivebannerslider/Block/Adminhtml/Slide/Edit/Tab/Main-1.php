<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slide\Edit\Tab;
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_systemStore;
    protected $_wysiwygConfig;
    protected $_slide;
    protected $_yesno;
    protected $_responsivebannerslider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
		\Magebees\Responsivebannerslider\Model\Slide $slide,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
         array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
		$this->_slide = $slide;
		$this->_yesno = $yesno;
		$this->_responsivebannerslider = $responsivebannerslider;
     
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
	protected function Groupsid() {
		$groups = $this->_responsivebannerslider->getCollection();
		
		if(count($groups)>0) {
			foreach($groups as $group) {
				$data = array(
					'value' => $group->getData('slidergroup_id'),
					'label' => $group->getTitle());
				$options[] = $data;		
			}
			return $options;
		}else{
			return false; 
		}
		
	}
	
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('slide_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
 
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Slide Information')]);
 
        if ($model->getId()) {
            $fieldset->addField('slide_id', 'hidden', ['name' => 'slide_id']);
        }
 
		$group_name = $fieldset->addField(
            'group_names',
            'multiselect',
            [
                'name' => 'group_names[]',
                'label' => __('Group'),
                'title' => __('Group'),
                'required' => true,
                'values' => $this->Groupsid(),
			]
        );
 
        $title = $fieldset->addField(
            'titles',
            'text',
            [
                'name' => 'titles',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
	 	$img_video = $fieldset->addField(
            'img_video',
            'select',
            [
                'name' => 'img_video',
                'label' => __('Image or Video'),
                'title' => __('Image or Video'),
                'required' => false,
                'values' => $this->_slide->getAvailableVideo(),
          	]
        );
		
		$img_hosting = $fieldset->addField(
            'img_hosting',
            'select',
            [
                'name' => 'img_hosting',
                'label' => __('Use External Image Hosting'),
                'title' => __('Use External Image Hosting'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
		
		
		$hosted_url = $fieldset->addField(
            'hosted_url',
            'text',
            [
                'name' => 'hosted_url',
                'label' => __('Hosted Image URL'),
                'title' => __('Hosted Image URL'),
                'required' => false,
				'note' => 'Ex - http://example.com/filename',
            ]
        );
		
		$hosted_thumb = $fieldset->addField(
            'hosted_thumb',
            'text',
            [
                'name' => 'hosted_thumb',
                'label' => __('Hosted Image Thumb URL'),
                'title' => __('Hosted Image Thumb URL'),
                'required' => false,
				'note' => 'you can use the same URL as above but for performance reasons it\'s better to upload a separate small thumbnail of this image, the thumbnails are used in carousels',
            ]
        );
	
		$video_id = $fieldset->addField(
            'video_id',
            'text',
            [
                'name' => 'video_id',
                'label' => __('Video ID'),
                'title' => __('Video ID'),
                'required' => false,
				'note' => 'Applied only if "Image or Video" field is set to "YouTube". Enter the video id of your YouTube or Vimeo video (not the full link)',
            ]
        );
		
		$filename = $fieldset->addField(
            'filename',
            'image',
            array(
                'name' => 'filename',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
            )
        );
		
		$filename_mobile = $fieldset->addField(
            'filename_mobile',
            'image',
            array(
                'name' => 'filename_mobile',
                'label' => __('Image for mobile'),
                'title' => __('Image for mobile'),
                'required' => false,
            )
        );
		
		$alt_text = $fieldset->addField(
            'alt_text',
            'text',
            [
                'name' => 'alt_text',
                'label' => __('ALT Text'),
                'title' => __('ALT Text'),
                'required' => false,
			]
        );
		
		$url = $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => false,
			]
        );
		
		
		
		 $url_target = $fieldset->addField(
            'url_target',
            'select',
            [
                'name' => 'url_target',
                'label' => __('URL Target'),
                'title' => __('URL Target'),
                'required' => false,
				'values' => $this->_slide->getUrlTarget(),
			]
        );

          $content_position = $fieldset->addField(
            'content_position',
            'select',
            [
                'name' => 'content_position',
                'label' => __('Content Position'),
                'title' => __('Content Position'),
                'required' => false,
                'values' => $this->_slide->getContentPosition(),
            ]
        );
 
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
 
         $description = $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
				'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:24em;',
                'required' => false,
                'config' => $wysiwygConfig
            ]
        );

          $btnone_text = $fieldset->addField(
            'btnone_text',
            'text',
            [
                'name' => 'btnone_text',
                'label' => __('Button 1 Text'),
                'title' => __('Button 1 Text'),
                'required' => false,
            ]
        );

         $btnone_url = $fieldset->addField(
            'btnone_url',
            'text',
            [
                'name' => 'btnone_url',
                'label' => __('Button 1 URL'),
                'title' => __('Button 1 URL'),
                'required' => false,
            ]
        );

         $btntwo_text = $fieldset->addField(
            'btntwo_text',
            'text',
            [
                'name' => 'btntwo_text',
                'label' => __('Button 2 Text'),
                'title' => __('Button 2 Text'),
                'required' => false,
            ]
        );

          $btntwo_url = $fieldset->addField(
            'btntwo_url',
            'text',
            [
                'name' => 'btntwo_url',
                'label' => __('Button 2 URL'),
                'title' => __('Button 2 URL'),
                'required' => false,
            ]
        ); 
 
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element'
        )->setTemplate(
            'Magento_Cms::page/edit/form/renderer/content.phtml'
        );
        $description->setRenderer($renderer);
 
 
		$date_enabled = $fieldset->addField(
            'date_enabled',
            'select',
            [
                'name' => 'date_enabled',
                'label' => __('Use Date Range'),
                'title' => __('Use Date Range'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
            ]
        );
		 $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
       
	    $from_date = $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
		
		$to_date = $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To Date'),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );
 
		$sort_order = $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
			]
        );
		
		$status = $fieldset->addField(
            'statuss',
            'select',
            array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'statuss',
                'required' => true,
                'options' => $this->_slide->getAvailableStatuses(),
            )
        );
		$model_data = $model->getData();
		if(count($model_data)>0) {
			if($model_data['filename'] != "") {
				$imgpath = "responsivebannerslider".$model_data['filename'];
				array_push($model_data,$model_data['filename'] = $imgpath);
			}
			if($model_data['filename_mobile'] != "") {
				$imgpaths = "responsivebannerslider".$model_data['filename_mobile'];
				array_push($model_data,$model_data['filename_mobile'] = $imgpaths);
			}
		}
		
		
		
        $form->setValues($model_data);
        $this->setForm($form);
		
		  $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($img_video->getHtmlId(), $img_video->getName())
            ->addFieldMap($img_hosting->getHtmlId(), $img_hosting->getName())
            ->addFieldMap($video_id->getHtmlId(), $video_id->getName())
            ->addFieldMap($hosted_url->getHtmlId(), $hosted_url->getName())
            ->addFieldMap($hosted_thumb->getHtmlId(), $hosted_thumb->getName())
            ->addFieldMap($filename->getHtmlId(), $filename->getName())
            ->addFieldMap($filename_mobile->getHtmlId(), $filename_mobile->getName())
            ->addFieldMap($alt_text->getHtmlId(), $alt_text->getName())
            ->addFieldMap($url->getHtmlId(), $url->getName())
            ->addFieldMap($url_target->getHtmlId(), $url_target->getName())
            ->addFieldMap($description->getHtmlId(), $description->getName())
			->addFieldMap($to_date->getHtmlId(), $to_date->getName())
			->addFieldMap($from_date->getHtmlId(), $from_date->getName())
			->addFieldMap($date_enabled->getHtmlId(), $date_enabled->getName())
			->addFieldDependence(
                $to_date->getName(),
                $date_enabled->getName(),
                1
            )
			->addFieldDependence(
                $from_date->getName(),
                $date_enabled->getName(),
                1
            )
            /* ->addFieldDependence(
                $video_id->getName(),
                $img_video->getName(),
                array('youtube','vimeo')
            )
			->addFieldDependence(
                $video_height->getName(),
                $img_video->getName(),
                array('youtube','vimeo')
            ) */
			->addFieldDependence(
                $img_hosting->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $hosted_url->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $hosted_thumb->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $filename->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $filename_mobile->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $alt_text->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $url->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $url_target->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $description->getName(),
                $img_video->getName(),
                'image'
            )
			->addFieldDependence(
                $hosted_url->getName(),
                $img_hosting->getName(),
                1
            )
			->addFieldDependence(
                $hosted_thumb->getName(),
                $img_hosting->getName(),
                1
            )
			->addFieldDependence(
                $filename->getName(),
                $img_hosting->getName(),
                0
            )
		);	
 
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Slide Information');
    }
 
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Slide Information');
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