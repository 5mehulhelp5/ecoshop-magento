<?php
namespace Magebees\Testimonial\Block;

class Testimonial extends \Magento\Framework\View\Element\Template
{
    protected $_collection;
	 protected $session;
	 protected $pager;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Testimonial\Model\ResourceModel\Testimonialcollection\CollectionFactory $collection,
        \Magento\Backend\Model\Session $session,
        array $data = []
    ) {
    
        $this->_collection = $collection;
        $this->session = $session;
        parent::__construct($context, $data);
    }
	
	protected function _beforeToHtml()
    {
        if ($this->getType()=="Magebees\Testimonial\Block\Widget\Testimonialwidget\Interceptor") {
            $this->setWidgetOptions();
        } elseif ($this->getType()=="Magebees\Testimonial\Block\Widget\Testimonialwidget") {
            $this->setWidgetOptions();
        } else {
            $this->setConfigValues();
        }
		 $this->setLoadedTestimonialColl($this->getPaginatedCollection());
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
       
      
        return $this;
    }

    public function getPagerHtml()
    {
		  $this->_testimonialConfig=$this->getTestimonialConfig();
          $pagination=$this->_testimonialConfig['per_page_value'];     
          $page_arr=explode(",", $pagination);
          $limit=[];
        foreach ($page_arr as $page) {
            $limit[$page]=$page;
        }
        if ($this->getLoadedTestimonialColl()->getSize()) {
            if (!$this->pager) {
                 $this->pager = $this->getLayout()->createBlock(
                     'Magento\Catalog\Block\Product\Widget\Html\Pager',
                     'magebees_testimonial.pager'
                 );

                $this->pager->setAvailableLimit($limit)
                ->setLimitVarName('ct_limit')
                ->setPageVarName('ct')
                ->setShowPerPage(true)                
                ->setCollection($this->getLoadedTestimonialColl());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
		
		
    }
    public function limit_word($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
    
    public function getPaginatedCollection()
    {
        $testimonial_ids=$this->getVisibleIds();
        $result=$this->_collection->create()->addFieldToFilter('status', 2)
        ->addFieldToFilter('testimonial_id', ['in' =>$testimonial_ids]);
		
		
		  $this->_testimonialConfig=$this->getTestimonialConfig();
          $pagination=$this->_testimonialConfig['per_page_value'];		
            $page_arr=explode(",", $pagination);
            $limit=[];
            foreach ($page_arr as $page) {
                $limit[$page]=$page;
            }
            $default_limit=current($limit);
         //get values of current page. if not the param value then it will set to 1
            $page=($this->getRequest()->getParam('ct'))? $this->getRequest()->getParam('ct') : 1;
        //get values of current limit. if not the param value then it will set to 1
            $pageSize=($this->getRequest()->getParam('ct_limit'))? $this->getRequest()->getParam('ct_limit') :$default_limit;
            $result->setPageSize($pageSize);
            $result->setCurPage($page);
        /**Get the customer data from database in custom register **/
        return $result;
    }
    

    public function getConfig()
    {
        return $this->_scopeConfig->getValue('testimonial/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function _toHtml()
    {
    
        $this->_config=$this->getConfig();
        if ($this->_config['enable']=="0") {
            return '';
        }

        if (!$this->getTemplate()) {
            $this->setTemplate('testimonial_detail.phtml');
        }
        return parent::_toHtml();
    }
    public function setWidgetOptions()
    {
        $this->setSlidemode((int)$this->getWdSlidemode());
        $this->setEnabled((bool)$this->getWdEnabled());
        $this->setTestimonialTitle($this->getWdTitleTestimonial());
        $this->setContentLength((int)$this->getWdContentLength());
        $this->setShowEmail((bool)$this->getWdEmail());
        $this->setShowAddress((bool)$this->getWdAddress());
        $this->setShowCreatedate((bool)$this->getWdCreatedDate());
        $this->setShowCompany((bool)$this->getWdCompany());
        $this->setShowWebsite((bool)$this->getWdWebsite());
        $this->setShowProfile((bool)$this->getWdProfile());
        $this->setShowRating((bool)$this->getWdRating());
        $this->setShowVideo((bool)$this->getWdYoutubeVideo());
        $this->setAutoplayVideo((bool)$this->getWdAutoplayVideo());
        $this->setShowNavArrow((bool)$this->getWdNavarrowSlider());
		$this->setShowPagination((bool)$this->getWdPaginationSlider());
		$this->setPaginationType((bool)$this->getWdPaginationType());
		$this->setAutoplaySlider((bool)$this->getWdAutoplaySlider());
		$this->setAutoplayoff((bool)$this->getWdAutoplayoff());
		$this->setSlideAutoHeight((bool)$this->getWdSlideAutoHeight());
		$this->setInfiniteLoop((bool)$this->getWdInfiniteLoop());
		$this->setScrollbar((bool)$this->getWdScrollbar());
		$this->setSliderSpeed((int)$this->getWdDelaytime());
        $this->setImageWidth((int)$this->getWdImgWidth());
        $this->setImageHeight((int)$this->getWdImgHeight());
		$this->setGrabCursor((int)$this->getWdGrabCursor());
		
    }
    
    public function setConfigValues()
    {
        $this->_testimonialConfig=$this->getTestimonialConfig();
        $this->_config=$this->getConfig();
        $this->setEnabled((bool)$this->_config['enable']);
        $this->setTestimonialTitle($this->_testimonialConfig['title_slider']);
        $this->setTestimonialFormTitle($this->_testimonialConfig['title_form_testimonial']);
        $this->setSlidemode((int)$this->_testimonialConfig['slider_mode']);
        $this->setContentLength((int)$this->_testimonialConfig['content_length']);
        $this->setShowEmail((bool)$this->_testimonialConfig['display_email']);
        $this->setShowAddress((bool)$this->_testimonialConfig['display_address']);
        $this->setShowCreatedate((bool)$this->_testimonialConfig['display_datetime']);
        $this->setShowCompany((bool)$this->_testimonialConfig['display_company']);
        $this->setShowWebsite((bool)$this->_testimonialConfig['display_website']);
        $this->setShowProfile((bool)$this->_testimonialConfig['display_profile_img']);
        $this->setShowRating((bool)$this->_testimonialConfig['display_rating']);
        $this->setShowVideo((bool)$this->_testimonialConfig['display_video']);
        $this->setAutoplayVideo((bool)$this->_testimonialConfig['autoplay_video']);
        $this->setShowNavArrow((bool)$this->_testimonialConfig['nav_arrow']);
        $this->setShowPagination((bool)$this->_testimonialConfig['slider_pagination']);
        $this->setAutoplaySlider((bool)$this->_testimonialConfig['autoplay_slider']);
        $this->setSliderSpeed((int)$this->_testimonialConfig['slider_delay']);
        $this->setImageWidth((int)$this->_testimonialConfig['profile_img_width']);
        $this->setImageHeight((int)$this->_testimonialConfig['profile_img_height']);
    }
    
   
    
    public function getTestimonialConfig()
    {
        return $this->_scopeConfig->getValue('testimonial/frontend_settings', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getRefreshUrl()
    {
        return $this->getUrl('testimonial/index/refresh', ['_secure' => true]);
    }
    
    
    public function getTestimonialCollection()
    {
        $testimonialCollection=$this->_collection->create();
        $testimonial_ids=$this->getVisibleIds();
        if (!empty($testimonial_ids)) {
            $finalCollection=$testimonialCollection
            ->addFieldToFilter('testimonial_id', ['in' =>$testimonial_ids])
            ->addFieldToFilter('status', 2);
        } else {
            $finalCollection=[];
        }
        return $finalCollection;
    }
    public function getVisibleIds()
    {
        $testimonial_ids=[];
        $storeId=$this->_storeManager->getStore()->getId();
        $testimonialCollection=$this->_collection->create();        
        $request =$this->getRequest();
        foreach ($testimonialCollection as $Collection) {
            $show_testimonial=false;
            if($this->getType()=='Magebees\Testimonial\Block\Widget\Testimonialwidget')
             {
                 if($Collection['enabled_widget']==1) {
                     $show_testimonial=true;
                 }
                 else {
                           $show_testimonial=false;
                       }  
                  if ($request->getFullActionName() == 'cms_index_index')  { 
                    if(($Collection['enabled_home']==1)&&($Collection['enabled_widget']==1)) {
                        $show_testimonial=true;
                    }    
                    else {
                           $show_testimonial=false;
                       }   
                }

             } else {
                
                if ($request->getFullActionName() == 'cms_index_index')  { 
                    if($Collection['enabled_home']==1) {
                        $show_testimonial=true;
                    }       
                } else {
                    $show_testimonial=true; }
            }
            if($show_testimonial)
            {
                 if (strpos($Collection['stores'], ',') !== false) {
                $store_arr=explode(",", $Collection['stores']);
                if (in_array($storeId, $store_arr)) {
                    $testimonial_ids[]=$Collection['testimonial_id'];
                }
				} elseif (($Collection['stores']==$storeId) || ($Collection['stores']==0)) {
					$testimonial_ids[]=$Collection['testimonial_id'];
				}

            }
        }
        return $testimonial_ids;
    }
    //add form data into session
    public function getFormData()
    {
        
        $data = $this->session->getTestimonialFormData();
        $this->session->setTestimonialFormData(null);
        return $data;
    }
    public function getFormUrl()
    {
        return $this->getUrl('testimonial/index/form', ['_secure' => true]);
    }    
   
}
