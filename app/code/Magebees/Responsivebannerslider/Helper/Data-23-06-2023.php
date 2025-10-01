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
			$navpos = "navArwInRight";
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
			
			$htmlcontent = '<div class="cwsRwdSlider swiper '.$navpos.' '.$navarrow.' '.$showpage.' '.$pagepos.'" id="cwsslider-'.$groupId.'">';
			$htmlcontent .= '<div class="swiper-wrapper '.$grab_cursor.'">';
  
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
						$htmlcontent .='<div class="videoWrapper"><iframe class="youtube" id="youtube_'.$videoid.'" src="https://www.youtube.com/embed/'. $slide->getVideoId().'?enablejsapi=1&amp;wmode=opaque&amp;playerapiid=youtube_'.$videoid.'" width="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
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
								if($slide->getUrlTarget() == "new_window") {
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

									$htmlcontent .='<a href="'.$btnoneUrl.'" class="btn_one action primary">'.$btnoneText.'</a>';						
								}	
								if($slide->getBtntwoText()) { 
									$btntwoUrl = $slide->getBtntwoUrl();
									$btntwoText = $slide->getBtntwoText();
									$htmlcontent .='<a href="'.$btntwoUrl.'" class="btn_two action primary">'.$btntwoText.'</a>';
								}
								$htmlcontent .='</div></div></div></div></div>';	
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
									if($slide->getUrlTarget() == "new_window") {
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
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
		return $mediaDirectory.'responsivebannerslider'.$imageName;
    }
	
	public function getThumbnailsImage($imageName) {
	
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
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
