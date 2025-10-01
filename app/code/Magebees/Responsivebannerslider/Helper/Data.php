<?php
namespace Magebees\Responsivebannerslider\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\SerializerInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
	protected $date;
	protected $_filesystem;
	protected $responsivebannerslider;
	protected $_imageFactory;
	protected $slide;
	protected $serializer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
        \Magebees\Responsivebannerslider\Model\Slide $slide,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		Filesystem $filesystem,
		SerializerInterface $serializer
       
    ) {
		$this->_storeManager = $storeManager;
		$this->_filesystem = $filesystem;
		$this->responsivebannerslider = $responsivebannerslider;
		$this->_imageFactory = $imageFactory;
		$this->slide = $slide;
		$this->date = $date;
		$this->serializer = $serializer;
    
        parent::__construct($context);
    	
	}

	public function getCssPath($groupId){
		$cssurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."responsivebannerslider/css/group-".$groupId.".css";
				
		return $cssurl;
	}


	public function getStaticTemplateDirectoryPath($dir_for_dynamic_file='pub/media/responsivebannerslider/files')
    {
		$rootPath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath().$dir_for_dynamic_file."/static/";
		return $rootPath;
    }
	
	public function getStaticCssDirectoryPath($dir_for_dynamic_file='pub/media/responsivebannerslider/')
    {
		$rootPath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath().$dir_for_dynamic_file."/css/";
		return $rootPath;
    }
	

	public function generateStaticHtml($groups){
		
		
		
		 
		
		$isVideoAvailable = $this->isVideoAvailable();
		if ($groups ['navigation_style'] == "style1") {
			$navstyle = "style1";
		} elseif ($groups ['navigation_style'] == "style2") {
			$navstyle = "style2";
		} elseif ($groups ['navigation_style'] == "style3") {
			$navstyle = "style3";
		} elseif ($groups ['navigation_style'] == "style4") {
			$navstyle = "style4";
		} elseif ($groups ['navigation_style'] == "style5") {
			$navstyle = "style5";
		} elseif ($groups ['navigation_style'] == "style6") {
			$navstyle = "style6";
		} else {
			$navstyle = "style7";
		}

		if ($groups ['navigation_aposition'] == "inside") {
			$navpos = "navArwInside";
		} elseif ($groups ['navigation_aposition'] == "outside") {
			$navpos = "navArwOutside";
		} elseif ($groups ['navigation_aposition'] == "inside_left") {
			$navpos = "navArwInLeft";
		} else {
			$navpos = null;
		}
			
		if ($groups ['show_pagination'] == "never") {
			$showpage = "noPaging";
		} elseif ($groups ['show_pagination'] == "always") {
			$showpage = "alwaysPaging";
		} else {
			$showpage = "pagingOnHover";
		}
		
		if ($groups ['pagination_style'] == "circular") {
			$pagestyle = "circular";
		} elseif ($groups ['pagination_style'] == "squared") {
			$pagestyle = "squared";
		} elseif ($groups ['pagination_style'] == "circular_bar") {
			$pagestyle = "cir-bar";
		} elseif ($groups ['pagination_style'] == "numbers") {
			$pagestyle = "numbers";
		}elseif ($groups ['pagination_style'] == "progress_bar") {
			$pagestyle = "progress_bar";
		}elseif ($groups ['pagination_style'] == "fraction_bar") {
			$pagestyle = "fraction_bar";
		}else {
			$pagestyle = "squ-bar";
		}
		
		if ($groups ['pagination_position'] == "below") {
			$pagepos = "pagerBelow";
		} elseif ($groups ['pagination_position'] == "above") {
			$pagepos = "pagerAbove";
		} elseif ($groups ['pagination_position'] == "inside_top") {
			$pagepos = "pagerInTop";
		} elseif ($groups ['pagination_position'] == "inside_bottom") {
			$pagepos = "pagerInBottom";
		} elseif ($groups ['pagination_position'] == "inside_bottom_left") {
			$pagepos = "pagerInBottomleft";
		} else {
			$pagepos = "pagerInBottomright";
		}
		
		if ($groups ['slider_type'] == "basic") {
			$bannerStyle = "basSlider";
		} else {
			$bannerStyle = "slideNcrosel";
		}
		
		if ($groups ['animation_direction'] == "vertical") {
			$verticalSlider = "verticalSlider";
		}else {			
			$verticalSlider = "";
		}

		$grab_cursor = '';
		if($groups['grab']=="1") {
			$grab_cursor = 'show-grab_cursor';	
		}

		$sliedsdata = $this->getSlides($groups['slidergroup_id']);
		if(count($sliedsdata) > 0 ) {
			$groupId = $groups['slidergroup_id'];
			
			
			
			
			if ($groups ['navigation_arrow'] == "never") {
						$navarrow = "noNavArw";
			} elseif ($groups ['navigation_arrow'] == "always") {
						$navarrow = "alwaysNavArw";
			} else {
						$navarrow = "NavArwOnHover";
			}
			if($groups['slider_type'] != "basic"){
			$swiper_data_init = array();
			$swiper_data_init['slider_id'] = 'cwsslider-'.$groupId;	
			
			if($groups['lazy_load']) {
			$swiper_data_init['lazy'] = true;			    
			}
			if($groups['loop_slider']) {
				$swiper_data_init['loop'] = true;
			} 
			if($groups['space_between']) {
				$swiper_data_init['spaceBetween'] = $groups['space_between'];
			}
			if($navarrow != "noNavArw") { 
			$swiper_data_init['navigation']['nextEl'] = "#cws-next-".$groupId;
			$swiper_data_init['navigation']['prevEl'] = "#cws-prev-".$groupId;
			 }
		  	$data_swiper = "data-swiper=";
			$data_swiper_options = "'".json_encode($swiper_data_init)."'";
			
			$carousel_swiper_data_init = array();
			$carousel_swiper_data_init['slider_id'] = 'mySwiper-'.$groupId;	
			
			if($groups['lazy_load']) {
			$carousel_swiper_data_init['lazy'] = true;			    
			}
			if($groups['loop_slider']) {
				$carousel_swiper_data_init['loop'] = true;
			} 
			if($groups['space_between']) {
				$carousel_swiper_data_init['spaceBetween'] = $groups['space_between'];
			}
			if($groups['slides_per_view']) {
				$carousel_swiper_data_init['slidesPerView'] = $groups['slides_per_view'];
				 
				$swiper_data_init['breakpoints']['1920']['slidesPerView'] = $slider_pre_view;
				$swiper_data_init['breakpoints']['1680']['slidesPerView'] = $slider_pre_view;
				$breakpoint_value = array('1200','768','0',);
				$slider_pre_view = $groups['slides_per_view'];
				foreach($breakpoint_value as $value):
				$swiper_data_init['breakpoints'][$value]['slidesPerView'] = $slider_pre_view;
				if($slider_pre_view > 1){
					$slider_pre_view = $slider_pre_view - 1; 
				}
				endforeach;
				
			}
			
		
			$carousel_swiper_data_init['freeMode'] = true;
			$carousel_swiper_data_init['watchSlidesProgress'] = true;
		
		  	$data_carousel_swiper = "data-carousel-swiper=";
			$data_carousel_swiper_options = "'".json_encode($carousel_swiper_data_init)."'";
			
			
			}else{
			
			$swiper_data_init = array();
			$swiper_data_init['slider_id'] = 'cwsslider-'.$groupId;	
			if($groups['slides_per_view']) {
				$swiper_data_init['slidesPerView'] = $groups['slides_per_view'];
				$swiper_data_init['slidesPerGroupSkip'] = $groups['slides_per_view'];
				$breakpoint_value = array('1200','768','0',);
				$slider_pre_view = $groups['slides_per_view'];
				$swiper_data_init['breakpoints']['1920']['slidesPerView'] = $slider_pre_view;
				$swiper_data_init['breakpoints']['1680']['slidesPerView'] = $slider_pre_view;
				foreach($breakpoint_value as $value):
				$swiper_data_init['breakpoints'][$value]['slidesPerView'] = $slider_pre_view;
				if($slider_pre_view > 1){
					$slider_pre_view = $slider_pre_view - 1; 
				}
				endforeach;
				
			}
			
			if($groups['space_between']) {
				$swiper_data_init['spaceBetween'] = $groups['spaceBetween'];
			}
			$swiper_data_init['loopFillGroupWithBlank'] = true;
			$swiper_data_init['centeredSlides'] = false;
			if($groups['loop_slider']) {
				$swiper_data_init['loop'] = true;
			} 
			if($navarrow != "noNavArw") { 
			$swiper_data_init['navigation']['nextEl'] = "#cws-next-".$groupId;
			$swiper_data_init['navigation']['prevEl'] = "#cws-prev-".$groupId;
			 } 
			if($showpage != "noPaging") {
				if($groups['pagination_style']=='default'):		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'default';		
				elseif($groups['pagination_style']=='dynamic'):		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'dynamic';		
				elseif($groups['pagination_style']=='progress_bar'):		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'progressbar';	
				elseif($groups['pagination_style']=='fraction_bar'):		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'fraction';	
				elseif($groups['pagination_style']=='numbers'):		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'custom';	
				else:		
				$swiper_data_init['pagination_id'] = "#cws-pager-".$groupId;	
				$swiper_data_init['pagination_type'] = 'dynamic';		
				endif;
			}
			if($groups['scrollbar']) { 
			$swiper_data_init['scrollbar']['el'] = "#cws-scrollbar-".$groupId;
			$swiper_data_init['scrollbar']['hide'] = true;
			}
			if($groups['start_animation']) { 
			$swiper_data_init['autoplay']['delay'] = $groups['slide_duration'];
			$swiper_data_init['scrollbar']['disableOnInteraction'] = false;
			}
			if($verticalSlider) {
			$swiper_data_init['direction'] = "vertical";			    
			}
			if($groups['keyboard']) {
			$swiper_data_init['keyboard']['enabled'] = true;			    
			}
			if($groups['grab']) {
			$swiper_data_init['grabCursor'] = true;			    
			}
			if($groups['smooth_height']) {
			$swiper_data_init['autoHeight'] = true;			    
			}
			if($groups['lazy_load']) {
			$swiper_data_init['lazy'] = true;			    
			}
			if($isVideoAvailable){
			$swiper_data_init['isVideoAvailable'] = true;			    	
			}
			if($verticalSlider){
			$swiper_data_init['verticalSlider'] = true;			    	
			}
			if($groups['animation_type'] == 'fade'){
			$swiper_data_init['effect'] = "fade";			    	
			}else if($groups['animation_type'] == 'cube'){
			$swiper_data_init['effect'] = "cube";
			$swiper_data_init['cubeEffect']['shadow'] = true;
			$swiper_data_init['cubeEffect']['slideShadows'] = true;
			$swiper_data_init['cubeEffect']['shadowOffset'] = 20;
			$swiper_data_init['cubeEffect']['shadowScale'] = 0.94;
			}else if($groups['animation_type'] == 'coverflow'){
			$swiper_data_init['effect'] = "groups";
			$swiper_data_init['coverflowEffect']['rotate'] = 50;
			$swiper_data_init['coverflowEffect']['stretch'] = 0;
			$swiper_data_init['coverflowEffect']['depth'] = 100;
			$swiper_data_init['coverflowEffect']['modifier'] = 1;	
			}else if($groups['animation_type'] == 'flip'){
			$swiper_data_init['effect'] = "flip";	
			}else if($groups['animation_type'] == 'cards'){
			$swiper_data_init['effect'] = "cards";
			}else if($groups['animation_type'] == 'creative'){
			$swiper_data_init['effect'] = "creative";	
			$swiper_data_init['creativeEffect']['prev']['shadow'] = true;	
			$swiper_data_init['creativeEffect']['prev']['translate'] = [0, 0, -800];	
			$swiper_data_init['creativeEffect']['prev']['rotate'] = [180, 0, 0];	
			$swiper_data_init['creativeEffect']['next']['shadow'] = true;	
			$swiper_data_init['creativeEffect']['next']['translate'] = [0, 0, -800];	
			$swiper_data_init['creativeEffect']['next']['rotate'] = [-180, 0, 0];	
			}
			
			}
			$data_swiper = "data-swiper=";
			$data_swiper_options = "'".json_encode($swiper_data_init)."'";
			
			if($groups['slider_type'] != "basic"){
			$htmlcontent = '<div class="cwsRwdSlider cws-no-animation swiper swiper-container '.$navpos.' '.$navarrow.' '.$showpage.' '.$pagepos.'" '.$data_swiper.$data_swiper_options.' '.$data_carousel_swiper.$data_carousel_swiper_options.' id="cwsslider-'.$groupId.'">';
			$htmlcontent .= '<div class="swiper-wrapper '.$grab_cursor.'">';
			}else{
			$htmlcontent = '<div class="cwsRwdSlider cws-no-animation swiper swiper-container '.$navpos.' '.$navarrow.' '.$showpage.' '.$pagepos.'" '.$data_swiper.$data_swiper_options.' id="cwsslider-'.$groupId.'">';
			$htmlcontent .= '<div class="swiper-wrapper '.$grab_cursor.'">';	
			}
			
  
			$youtube = 0;
			$vimeo = 0;
			$slide_count=0;
			$applied_slide_lazy_load = true;
			foreach ( $sliedsdata as $slide ) {
				$slide_count++;
				/* lazy load not applied on First slide on slider*/
				if($slide_count < 2){
						$applied_slide_lazy_load = false;
					}else{
						$applied_slide_lazy_load = true;
					}
				$htmlcontent .='<div class="swiper-slide">';
				if ($slide->getDateEnabled()) {
					$fromdate = strtotime ( $slide->getFromDate () );
					$todate = strtotime ( $slide->getToDate () );
					$nowdate = strtotime ($this->getCurrentDate() );
				} else {
					$fromdate = strtotime ($this->getCurrentDate());
					$todate = strtotime ($this->getCurrentDate());
					$nowdate = strtotime ($this->getCurrentDate());
				}
				$videoid = $groups ['slidergroup_id'];
				$video_height = $slide->getVideoHeight ();
				if($fromdate <= $nowdate && $todate >= $nowdate ) {
					if($slide->getImgVideo() == "youtube") { 
						$youtube = $youtube + 1; 
						$htmlcontent .='<div class="videoWrapper"><iframe class="js-youtube" id="cwsslider-'.$groupId.'-'.$slide_count.'" src="https://www.youtube.com/embed/'. $slide->getVideoId().'?enablejsapi=1&html5=1&rel=0" width="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen videocws></iframe></div>';
					}if($slide->getImgVideo() == "vimeo") { 
					
						$htmlcontent .='<div class="videoWrapper"><iframe class="js-vimeo" id="cwsslider-'.$groupId.'-'.$slide_count.'" src="https://player.vimeo.com/video/'. $slide->getVideoId().'?enablejsapi=1&html5=1&rel=0" width="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen videocws></iframe></div>';
					}else{
					   	if($slide->getData ( 'img_hosting' )) {
							if ($slide->getData ( 'hosted_url' )) {
								$imgsrc = $slide->getHostedUrl ();
								$height = $slide->getImageHeight();
								$width = $slide->getImageWidth();
							}
						}else{
							$mobileimgsrc = '';
							if ($slide->getData ( 'filename' )) {
								$imgsrc = $this->getBannerImage($slide->getData ( 'filename' ));
								$height = $slide->getImageHeight();
								$width = $slide->getImageWidth();
							}
							if ($slide->getData ( 'filename_mobile' )) {
								$mobileimgsrc = $this->getBannerImage($slide->getData ( 'filename_mobile' ));
								$height_mo = $slide->getMobileImageHeight();
								$width_mo = $slide->getMobileImageWidth();
							}
						}
						

						if($slide->getData('hosted_url') != "" || $slide->getData('filename') != "" || $slide->getData('filename_mobile') != "") {

							if($slide->getUrl()) { 
								$target= '';
								if($slide->getData('url_target') == "new_window") {
									$target="_blank";
								}
								$htmlcontent .='<a href="'.$slide->getUrl().'" title="'.$slide->getTitles().'" target="'.$target.'">';
							}

							
								
							$htmlcontent .='<picture>';
							if($mobileimgsrc) { 
								$htmlcontent .='<source media="(max-width:767px)" srcset="'.$mobileimgsrc.'" width="'.$width_mo.'" height="'.$height_mo.'">';
							}
							$lazy = '';
							$image_class = 'cwsslide';
							if(($groups['lazy_load']=="1")&&($applied_slide_lazy_load)) { 
								$lazy = 'lazy';
								$image_class .= ' swiper-lazy';
							}
							
							$alt =  $slide->getAltText();
							$htmlcontent .='<img class="'.$image_class.'" src="'.$imgsrc.'"  loading="'.$lazy.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'"/>';
							$htmlcontent .='</picture>';
							if($slide->getUrl()) {
								$htmlcontent .='</a>';
							}


							if($slide->getDescription()) {
								$content_position = $slide->getData('content_position');
								$desc = $slide->getDescription();

								$htmlcontent .='<div class="container">';
								$htmlcontent .='<div class="sliderContent '.$content_position.'">';
								$htmlcontent .='<div class="slideshow-content sliderdecs">';
								$htmlcontent .='<div class="wrap-caption animation">';
								$htmlcontent .='<div class="sliderdecsIn">'.$desc.'</div>';
								$htmlcontent .='<div class="button-sets">';
								if($slide->getBtnoneText()) { 
									$btnoneUrl = $slide->getBtnoneUrl();
									$btnoneText = $slide->getBtnoneText();

									$htmlcontent .='<a href="'.$btnoneUrl.'" class="btn_one action primary" aria-label="'.trim(strip_tags($btnoneText)).'">'.$btnoneText.'</a>';						
								}	
								if($slide->getBtntwoText()) { 
									$btntwoUrl = $slide->getBtntwoUrl();
									$btntwoText = $slide->getBtntwoText();
									$htmlcontent .='<a href="'.$btntwoUrl.'" class="btn_two action primary" aria-label="'.trim(strip_tags($btntwoText)).'">'.$btntwoText.'</a>';
								}
								$htmlcontent .='</div>';
								if($slide->getShowOfferText()) { 
									$offer_text = $slide->getOfferText();
									$htmlcontent .='<div class="offertext">'.$offer_text.'</div>';
								}
								$htmlcontent .='</div></div></div></div>';	
							}
							if(($groups['lazy_load']=="1")&&($applied_slide_lazy_load)) { 
								$htmlcontent .='<div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>';
							}
							
						}
						 if($slide->getDateEnabled() == 1) { } 	
					}	
			
				}
				$htmlcontent .='</div>';
				$youtube++;
				
			}
			
			$htmlcontent .='</div>';
			if($navarrow != "noNavArw") {
				$htmlcontent .='<div class="cws-arw '.$navarrow.' '.$navstyle.'">';
					$htmlcontent .='<div id="cws-next-'.$groupId.'" class="cws-next"></div>';
					$htmlcontent .='<div id="cws-prev-'.$groupId.'" class="cws-prev"></div>';
				$htmlcontent .='</div>';	
			}	
			if($showpage != "noPaging") {
				$htmlcontent .='<div id="cws-pager-'.$groupId.'" class="cws-pager cws-control-paging '.$showpage.' '.$pagestyle.' '.$pagepos.'"></div>';				
			}
			if($groups['scrollbar']=="1") {
				$htmlcontent .='<div id="cws-scrollbar-'.$groupId.'" class="swiper-scrollbar"></div>';				
			}	

			

			$htmlcontent .='</div>';
 
			// not basic

			if($groups['slider_type'] != "basic"){

				$htmlcontent .= '<div class="rwdCarousel mySwiper" id="mySwiper-'.$groupId.'">';

				$htmlcontent .= '<div class="swiper-wrapper">';
	 
				$youtube = 0;
				$vimeo = 0;
				
				$slide_count=0;
				$applied_slide_lazy_load = true;
				foreach ( $sliedsdata as $slide ) {
					$slide_count++;
					/* lazy load not applied on First slide on slider*/
					if($slide_count < 2){
						$applied_slide_lazy_load = false;
					}else{
						$applied_slide_lazy_load = true;
					}
					
					$htmlcontent .='<div class="swiper-slide">';
					if ($slide->getDateEnabled()) {
						$fromdate = strtotime ( $slide->getFromDate () );
						$todate = strtotime ( $slide->getToDate () );
						$nowdate = strtotime ($this->getCurrentDate() );
					} else {
						$fromdate = strtotime ($this->getCurrentDate());
						$todate = strtotime ($this->getCurrentDate());
						$nowdate = strtotime ($this->getCurrentDate());
					} 
					$videoid = $groups ['slidergroup_id'];
					$video_height = $slide->getVideoHeight ();
					if($fromdate <= $nowdate && $todate >= $nowdate ) {
							if($slide->getImgVideo() == "youtube") { 
							$youtube = $youtube + 1; 
							$htmlcontent .='<div class="videoWrapper"><a
					href="https://www.youtube.com/watch?v='.$slide->getVideoId().'"
					title="'.$slide->getTitles().'"	target="_blank"><img
						src="https://img.youtube.com/vi/'.$slide->getVideoId().'/0.jpg"
						alt="'.$slide->getTitles().'" /></a></div>';
						}if($slide->getImgVideo() == "vimeo") { 
						
							$vimeo = $vimeo + 1;
							$img = $slide->getVideoId ();
							$hash = unserialize ( file_get_contents ( "https://vimeo.com/api/v2/video/$img.php" ) );
					
						
						
						
						$htmlcontent .='<picture"><a
					href="https://vimeo.com/'.$slide->getVideoId().'"
					title="'.$slide->getTitles().'"	target="_blank"><img
						src="'.$hash[0]["thumbnail_large"].'"
						alt="'.$slide->getTitles().'" /></a></picture>';
					}else{
						   	if($slide->getData ( 'img_hosting' )) {
								if ($slide->getData ( 'hosted_url' )) {
									$imgsrc = $slide->getHostedUrl ();
									$height = $slide->getImageHeight();
									$width = $slide->getImageWidth();
								}
							}else{
								$mobileimgsrc = '';
								if ($slide->getData ( 'filename' )) {
									$imgsrc = $this->getBannerImage($slide->getData ( 'filename' ));
									$height = $slide->getImageHeight();
									$width = $slide->getImageWidth();
								}
								if ($slide->getData ( 'filename_mobile' )) {
									$mobileimgsrc = $this->getBannerImage($slide->getData ( 'filename_mobile' ));
									$height_mo = $slide->getMobileImageHeight();
									$width_mo = $slide->getMobileImageWidth();
								}
							}
							

							if($slide->getData('hosted_url') != "" || $slide->getData('filename') != "" || $slide->getData('filename_mobile') != "") {

								if($slide->getUrl()) { 
									$target= '';
									if($slide->getData('url_target') == "new_window") {
										$target="_blank";
									}
									$htmlcontent .='<a href="'.$slide->getUrl().'" title="'.$slide->getTitles().'" target="'.$target.'">';
								}
								
								$htmlcontent .='<picture>';
								if($mobileimgsrc) { 
									$htmlcontent .='<source media="(max-width:767px)" srcset="'.$mobileimgsrc.'" width="'.$width_mo.'" height="'.$height_mo.'">';
								}
								$lazy = '';
								$image_class = 'cwsslide';
								if(($groups['lazy_load']=="1")&&($applied_slide_lazy_load)) { 
									$lazy = 'lazy';
									$image_class .= ' swiper-lazy';
								}
								
								$alt =  $slide->getAltText();
								$htmlcontent .='<img class="'.$image_class.'" src="'.$imgsrc.'"  loading="'.$lazy.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'"/>';
								$htmlcontent .='</picture>';
								if($slide->getUrl()) {
									$htmlcontent .='</a>';
								}
								
								if(($groups['lazy_load']=="1")&&($applied_slide_lazy_load)) { 
									$htmlcontent .='<div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>';
								}
							}
							 if($slide->getDateEnabled() == 1) { } 	
						}	
				
					}
					$htmlcontent .='</div>';
					$youtube++;
					
				}
				$htmlcontent .='</div>';
			}

			return 	$htmlcontent;	
		}

		return true;	
		
	}

	public function getSlides($slidegroupId) {	
		$slide_collection = $this->slide->getCollection()
			->addFieldToFilter('group_names', array(array('finset' => $slidegroupId)))
			->addFieldToFilter('statuss', '1')
			->setOrder('sort_order','ASC');
		return $slide_collection;
	}


	public function getAllGroup(){
		$groupData =  [];
        $group_collection = $this->responsivebannerslider->getCollection();
            $groupData [] =  [
                    'value' => '',
                    'label' => 'Please Select Group',
            ];
            foreach ($group_collection as $group) {
                $groupData [] =  [
                    'value' => $group->getSlidergroupId(),
                    'label' => $group->getTitle(),
                ];
            }
            return $groupData;
		
		
	}

	public function getSerializeData($arr) {
	    $unSerializeData = $this->serializer->unserialize($arr);
	    return $unSerializeData;
	}

	public function enabledModule(){
		return $this->scopeConfig->getValue('responsivebannerslider/setting/enabled',ScopeInterface::SCOPE_STORE);
	}

	public function enabledDevmode(){
		return $this->scopeConfig->getValue('responsivebannerslider/optimize_performance/developer_mode_enable_disable',ScopeInterface::SCOPE_STORE);
	}
	
	public function getCurrentDate(){
		return $this->date->gmtDate();
	}
	
	public function getBannerImage($imageName) {
	
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA,true
            );
		return $mediaDirectory.'responsivebannerslider'.$imageName;
    }
	
	public function getThumbnailsImage($imageName) {
	
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA,true
            );
		return $mediaDirectory.'responsivebannerslider/thumbnails'.$imageName;
    }
	
	public function resizeImg($fileName) {
		$dir = "thumbnails";
		$width = $this->scopeConfig->getValue('responsivebannerslider/setting/thumbnail_width',ScopeInterface::SCOPE_STORE);;
		if(trim($width) == "" || trim($width) < 0){
			$width = "200";
		}
				
		$mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$bannerDir = '/responsivebannerslider';
		$mediaDir->create($bannerDir);
        $mediaDir->changePermissions($bannerDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
        $bannerDir = $mediaDir->getAbsolutePath($bannerDir);
	 	$absPath = $bannerDir.$fileName;
		$imageResized = $bannerDir."/".$dir.$fileName;
		
		if ($width != '') {
			
			if (file_exists($imageResized)) {
				unlink($imageResized);
			} 
			
			$imageResize = $this->_imageFactory->create();
			$imageResize->open($absPath);
			$imageResize->constrainOnly(TRUE);
			$imageResize->keepTransparency(TRUE);
			$imageResize->keepFrame(FALSE);
			$imageResize->keepAspectRatio(true);
			$imageResize->resize($width);
			$dest = $imageResized ;
			$imageResize->save($dest);
		}
		$path = $bannerDir."/".$dir;
		if( chmod($path, 0777) ) {
				chmod($path, 0755);
		}
		
		$paths = $bannerDir;
		if( chmod($paths, 0777) ) {
				chmod($paths, 0755);
		}
		
		return true;

	}
	public function isVideoAvailable(){
		$sliderCollection = $this->slide->getCollection()
			->addFieldToFilter('img_video', array('eq'=> 'youtube'))
			->addFieldToFilter('video_id', array('neq'=> null));
		return $sliderCollection->getSize();
	}	
	public function getCurrentUrl(){
		return $this->_urlBuilder->getCurrentUrl();
	}
	
	public function isMobile(){
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	
}
