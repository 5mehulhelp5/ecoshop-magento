<?php
namespace Magebees\CategoryImage\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class CategoryImagewidget extends Template  implements BlockInterface
{
   
		
		
	 public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		 \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->categoryFactory = $categoryFactory;
		//$this->setTemplate('categories_list.phtml');
    	
    }
	
	public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        $this->_data[$key] = $value;
	}
	protected function getCacheLifetime()
	{
		return 84600;
	}
	public function getCacheKeyInfo()
	{
		$name =   'magebees_category_image_widget';
		$keyInfo     =  parent::getCacheKeyInfo();
		$keyInfo[]   =  $name; // adding the product id to the cache key so that each cache built remains unique as per the product page.
		
		return $keyInfo;
	}
	public function getCategoriesArray(){
		$res = explode(",",(string)$this->getData('wd_categories'));
		return $res;
	}
	
	public function getCategoriesCollection($categoryId)
    {
        $cat_collection = $this->categoryFactory->create()->load($categoryId);
		return $cat_collection;
    }
	public function getRandomId(){
		return $random_number = rand();
	}
	public function getSwiperOptions($random_number,$slider_id){

		$no_of_items = $this->getData('wd_no_of_items')?$this->getData('wd_no_of_items'):10;
		$autoscroll = $this->getData('wd_autoscroll')=='1' ? true:false;
		$navarrow = $this->getData('wd_navarrow')=='1' ? true:false;
		$pagination = $this->getData('wd_pagination')=='1' ? true:false;
		$paginationtype = $this->getData('wd_pagination_type');
		$infinite_loop = $this->getData('wd_infinite_loop')=='1' ? true:false;
		$scrollbar = $this->getData('wd_scrollbar')=='1' ? true:false;
		$grap_cursor = $this->getData('wd_grap_cursor')=='1' ? true:false;
		$auto_height = $this->getData('wd_slide_auto_height')=='1' ? true:false;
		$centered = $this->getData('wd_centered')=='1' ? true:false;
		$items = $this->getData('wd_items_per_slide');
		$auto_play_delaytime = $this->getData('wd_auto_play_delaytime') ? $this->getData('wd_auto_play_delaytime'):3000;
		$autoplayoff = $this->getData('wd_autoplayoff')=='1' ? true:false;
		$items_per_row = 'category-simple-img category-items-'.$this->getData('wd_items_per_row');

		$swiper_data_init = array();
		$swiper_data_init['slider_id'] = $slider_id;
		$swiper_data_init['autoHeight'] = $this->getData('wd_slide_auto_height')=='1' ? true:false;
		if($this->getData('template')=='Magebees_CategoryImage::style1.phtml'):
			$swiper_data_init['slidesPerView'] = 'auto';
			$swiper_data_init['freeMode'] = true;
		endif;
			$swiper_data_init['spaceBetween'] = 30;
			$swiper_data_init['lazy']['loadPrevNext'] = true;
			$swiper_data_init['lazy']['loadPrevNextAmount'] = $no_of_items;
			$swiper_data_init['a11y'] = false;
			$swiper_data_init['speed'] = 600;
			$swiper_data_init['loop'] = $infinite_loop;
			$swiper_data_init['grabCursor'] = $grap_cursor;
			$swiper_data_init['centeredSlides'] = $centered;
		 if($autoscroll):
			$swiper_data_init['autoplay']['delay'] = $auto_play_delaytime;
			$swiper_data_init['autoplay']['disableOnInteraction'] = false;
			$swiper_data_init['autoplay']['pauseOnMouseEnter'] = $autoplayoff;
		endif;
		if($scrollbar):
		$swiper_data_init['scrollbar']['el'] = "#swiper-scrollbar-".$random_number;
		$swiper_data_init['scrollbar']['hide'] = false;
		endif;
		if($paginationtype=='default'):
		$swiper_data_init['pagination_id'] = "#swiper-pagination-".$random_number;
		$swiper_data_init['pagination_type'] = $paginationtype;
		elseif($paginationtype=='dynamic'):
		$swiper_data_init['pagination_id'] = "#swiper-pagination-".$random_number;
		$swiper_data_init['pagination_type'] = $paginationtype;
		elseif($paginationtype=='progress'):
		$swiper_data_init['pagination_id'] = "#swiper-pagination-".$random_number;
		$swiper_data_init['pagination_type'] = $paginationtype;
		elseif($paginationtype=='fraction'):
		$swiper_data_init['pagination_id'] = "#swiper-pagination-".$random_number;
		$swiper_data_init['pagination_type'] = $paginationtype;
		elseif($paginationtype=='custom'):
		$swiper_data_init['pagination_id'] = "#swiper-pagination-".$random_number;
		$swiper_data_init['pagination_type'] = $paginationtype;
		endif;
		if($navarrow):
		$swiper_data_init['navigation']['nextEl'] = "#swiper-button-next-".$random_number;
		$swiper_data_init['navigation']['prevEl'] = "#swiper-button-prev-".$random_number;
		endif;

		return json_encode($swiper_data_init);
		
		
	}
	protected function _beforeToHtml()
    {	
		
		//print_r($widgetData);die;
		$this->load_ajax = $this->getData('wd_load_ajax');
		if($this->load_ajax){
		$this->setTemplate('Magebees_CategoryImage::load_category_list.phtml');
		return parent::_beforeToHtml();
		}
		
	}
	
}