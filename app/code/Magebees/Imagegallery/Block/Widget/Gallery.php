<?php
namespace Magebees\Imagegallery\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class Gallery extends \Magebees\Imagegallery\Block\Gallery implements BlockInterface {
    protected $_template = "widget/gallery.phtml";
	
    public $template=null;
    public $wd_spacing=null;
    public $wd_bottom_spacing=null;
    public $wd_show_heading=null;
	public $wd_heading=null;
    public $wd_show_description=null;
	public $wd_description=null;
	
	public $no_of_image=null;
	public $wd_slider=null;
	public $wd_items_per_slide=null;
    public $wd_items_per_row=null;
    public $wd_autoscroll=null;
	public $wd_auto_play_delaytime=null;
	public $wd_autoplayoff=null;
	public $wd_slide_auto_height=null;
	public $wd_navarrow=null;
	public $wd_pagination=null;
	public $wd_pagination_type=null;
	public $wd_infinite_loop=null;
	public $wd_scrollbar=null;
	public $wd_grap_cursor=null;
	public $store_id=null;
	public $wd_bgcolor=null;
	public $wd_bgimage=null;
	
	
    public function getGalleyImageCollection($limit=null) {
        
		$storeId = $this->_storemanager->getStore()->getId();
		$collection = $this->imagegallery->getCollection();
		$collection->addFieldToFilter('status', array('eq' => 1));
		$collection->addFieldToFilter(['stores', 'stores'], [['finset' => 0], ['finset' => $storeId]]);
		$collection->setOrder('sort_order', 'ASC');
		if($limit):
			$collection->setPageSize($limit);
		endif;
		return $collection;
    }
	
	public function _toHtml()
    {
		$load_ajax = $this->getData('wd_load_ajax');
		if($load_ajax){
			$this->setTemplate('Magebees_Imagegallery::load_gallery_list.phtml');
		}else if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }
}

