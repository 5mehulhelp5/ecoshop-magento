<?php
namespace Magebees\CategoryImage\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class AjaxLoad extends Action
{
 
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
	
	

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(    
        Context $context, 
        PageFactory $resultPageFactory, 
        JsonFactory $resultJsonFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
 
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
			
		$categoryThumbnail = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		if($this->getRequest()->getParam('wd_enable')):
		$categoryThumbnail->setData('wd_enable',$this->getRequest()->getParam('wd_enable'));
		endif;
		if($this->getRequest()->getParam('wd_spacing')):
		$categoryThumbnail->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$categoryThumbnail->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_shopnow')):
		$categoryThumbnail->setData('wd_shopnow',$this->getRequest()->getParam('wd_shopnow'));
		endif;
		if($this->getRequest()->getParam('store_id')):
		$categoryThumbnail->setData('store_id',$this->getRequest()->getParam('store_id'));
		endif;
		
		if($this->getRequest()->getParam('wd_categories')):
		$categoryThumbnail->setData('wd_categories',$this->getRequest()->getParam('wd_categories'));
		endif;
		if($this->getRequest()->getParam('wd_show_heading')):
		$categoryThumbnail->setData('wd_show_heading',$this->getRequest()->getParam('wd_show_heading'));
		endif;
		if($this->getRequest()->getParam('wd_heading')):
		$categoryThumbnail->setData('wd_heading',$this->getRequest()->getParam('wd_heading'));
		endif;
		if($this->getRequest()->getParam('wd_heading_position')):
		$categoryThumbnail->setData('wd_heading_position',$this->getRequest()->getParam('wd_heading_position'));
		endif;
		if($this->getRequest()->getParam('wd_heading_logo')):
		$categoryThumbnail->setData('wd_heading_logo',$this->getRequest()->getParam('wd_heading_logo'));
		endif;
		if($this->getRequest()->getParam('wd_short_desc')):
		$categoryThumbnail->setData('wd_short_desc',$this->getRequest()->getParam('wd_short_desc'));
		endif;
		if($this->getRequest()->getParam('wd_show_viewall')):
		$categoryThumbnail->setData('wd_show_viewall',$this->getRequest()->getParam('wd_show_viewall'));
		endif;
		if($this->getRequest()->getParam('wd_viewall_txt')):
		$categoryThumbnail->setData('wd_viewall_txt',$this->getRequest()->getParam('wd_viewall_txt'));
		endif;
		if($this->getRequest()->getParam('wd_viewall_url')):
		$categoryThumbnail->setData('wd_viewall_url',$this->getRequest()->getParam('wd_viewall_url'));
		endif;
		if($this->getRequest()->getParam('wd_no_of_items')):
		$categoryThumbnail->setData('wd_no_of_items',$this->getRequest()->getParam('wd_no_of_items'));
		endif;
		if($this->getRequest()->getParam('wd_bgimage')):
		$categoryThumbnail->setData('wd_bgimage',$this->getRequest()->getParam('wd_bgimage'));
		endif;
		if($this->getRequest()->getParam('wd_description')):
		$categoryThumbnail->setData('wd_description',$this->getRequest()->getParam('wd_description'));
		endif;
		if($this->getRequest()->getParam('wd_slider')):
		$categoryThumbnail->setData('wd_slider',$this->getRequest()->getParam('wd_slider'));
		endif;
		if($this->getRequest()->getParam('wd_autoscroll')):
		$categoryThumbnail->setData('wd_autoscroll',$this->getRequest()->getParam('wd_autoscroll'));
		endif;
		if($this->getRequest()->getParam('wd_navarrow')):
		$categoryThumbnail->setData('wd_navarrow',$this->getRequest()->getParam('wd_navarrow'));
		endif;
		if($this->getRequest()->getParam('wd_pagination')):
		$categoryThumbnail->setData('wd_pagination',$this->getRequest()->getParam('wd_pagination'));
		endif;
		if($this->getRequest()->getParam('wd_pagination_type')):
		$categoryThumbnail->setData('wd_pagination_type',$this->getRequest()->getParam('wd_pagination_type'));
		endif;
		if($this->getRequest()->getParam('wd_infinite_loop')):
		$categoryThumbnail->setData('wd_infinite_loop',$this->getRequest()->getParam('wd_infinite_loop'));
		endif;
		if($this->getRequest()->getParam('wd_scrollbar')):
		$categoryThumbnail->setData('wd_scrollbar',$this->getRequest()->getParam('wd_scrollbar'));
		endif;
		if($this->getRequest()->getParam('wd_grap_cursor')):
		$categoryThumbnail->setData('wd_grap_cursor',$this->getRequest()->getParam('wd_grap_cursor'));
		endif;
		if($this->getRequest()->getParam('wd_slide_auto_height')):
		$categoryThumbnail->setData('wd_slide_auto_height',$this->getRequest()->getParam('wd_slide_auto_height'));
		endif;
		if($this->getRequest()->getParam('wd_centered')):
		$categoryThumbnail->setData('wd_centered',$this->getRequest()->getParam('wd_centered'));
		endif;
		if($this->getRequest()->getParam('wd_items_per_slide')):
		$categoryThumbnail->setData('wd_items_per_slide',$this->getRequest()->getParam('wd_items_per_slide'));
		endif;
		if($this->getRequest()->getParam('wd_auto_play_delaytime')):
		$categoryThumbnail->setData('wd_auto_play_delaytime',$this->getRequest()->getParam('wd_auto_play_delaytime'));
		endif;
		if($this->getRequest()->getParam('wd_autoplayoff')):
		$categoryThumbnail->setData('wd_autoplayoff',$this->getRequest()->getParam('wd_autoplayoff'));
		endif;
		if($this->getRequest()->getParam('wd_items_per_row')):
		$categoryThumbnail->setData('wd_items_per_row',$this->getRequest()->getParam('wd_items_per_row'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
			$categoryThumbnail->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;
		if($this->getRequest()->getParam('wd_shopnow_text')):
			$categoryThumbnail->setData('wd_shopnow_text',$this->getRequest()->getParam('wd_shopnow_text'));
		endif;
		
		
		if($this->getRequest()->getParam('template')):
			$categoryThumbnail->setTemplate($this->getRequest()->getParam('template'));
		endif;
		$block = $categoryThumbnail->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}