<?php
namespace Magebees\PocoBase\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Blog extends Action
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
			
		$latestpost = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		if($this->getRequest()->getParam('template')):
			$latestpost->setTemplate($this->getRequest()->getParam('template'));
		endif;
		if($this->getRequest()->getParam('wd_spacing')):
			$latestpost->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
			$latestpost->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_custom_link')):
			$latestpost->setData('wd_custom_link',$this->getRequest()->getParam('wd_custom_link'));
		endif;
		if($this->getRequest()->getParam('wd_custom_title')):
			$latestpost->setData('wd_custom_title',$this->getRequest()->getParam('wd_custom_title'));
		endif;
		if($this->getRequest()->getParam('wd_custom_link_title')):
			$latestpost->setData('wd_custom_link_title',$this->getRequest()->getParam('wd_custom_link_title'));
		endif;
		if($this->getRequest()->getParam('wd_custom_link_url')):
			$latestpost->setData('wd_custom_link_url',$this->getRequest()->getParam('wd_custom_link_url'));
		endif;
		if($this->getRequest()->getParam('wd_show_heading')):
			$latestpost->setData('wd_show_heading',$this->getRequest()->getParam('wd_show_heading'));
		endif;
		if($this->getRequest()->getParam('wd_heading')):
			$latestpost->setData('wd_heading',$this->getRequest()->getParam('wd_heading'));
		endif;
		if($this->getRequest()->getParam('wd_show_description')):
			$latestpost->setData('wd_show_description',$this->getRequest()->getParam('wd_show_description'));
		endif;
		if($this->getRequest()->getParam('wd_description')):
			$latestpost->setData('wd_description',$this->getRequest()->getParam('wd_description'));
		endif;
		
		if($this->getRequest()->getParam('wd_post_show_feature_image')):
			$latestpost->setData('wd_post_show_feature_image',$this->getRequest()->getParam('wd_post_show_feature_image'));
		endif;
		if($this->getRequest()->getParam('feature_image_height')):
			$latestpost->setData('feature_image_height',$this->getRequest()->getParam('feature_image_height'));
		endif;
		if($this->getRequest()->getParam('feature_image_width')):
			$latestpost->setData('feature_image_width',$this->getRequest()->getParam('feature_image_width'));
		endif;
		if($this->getRequest()->getParam('resize_type')):
			$latestpost->setData('resize_type',$this->getRequest()->getParam('resize_type'));
		endif;
		
		if($this->getRequest()->getParam('wd_post_type')):
			$latestpost->setData('wd_post_type',$this->getRequest()->getParam('wd_post_type'));
		endif;
		if($this->getRequest()->getParam('wd_post_limit')):
			$latestpost->setData('wd_post_limit',$this->getRequest()->getParam('wd_post_limit'));
		endif;
		if($this->getRequest()->getParam('wd_slider')):
			$latestpost->setData('wd_slider',$this->getRequest()->getParam('wd_slider'));
		endif;
		if($this->getRequest()->getParam('wd_items_per_slide')):
			$latestpost->setData('wd_items_per_slide',$this->getRequest()->getParam('wd_items_per_slide'));
		endif;
		
		if($this->getRequest()->getParam('wd_autoscroll')):
			$latestpost->setData('wd_autoscroll',$this->getRequest()->getParam('wd_autoscroll'));
		endif;
		if($this->getRequest()->getParam('wd_auto_play_delaytime')):
			$latestpost->setData('wd_auto_play_delaytime',$this->getRequest()->getParam('wd_auto_play_delaytime'));
		endif;
		if($this->getRequest()->getParam('wd_autoplayoff')):
			$latestpost->setData('wd_autoplayoff',$this->getRequest()->getParam('wd_autoplayoff'));
		endif;
		if($this->getRequest()->getParam('wd_slide_auto_height')):
			$latestpost->setData('wd_slide_auto_height',$this->getRequest()->getParam('wd_slide_auto_height'));
		endif;
		if($this->getRequest()->getParam('wd_navarrow')):
			$latestpost->setData('wd_navarrow',$this->getRequest()->getParam('wd_navarrow'));
		endif;
		
		if($this->getRequest()->getParam('wd_pagination')):
			$latestpost->setData('wd_pagination',$this->getRequest()->getParam('wd_pagination'));
		endif;
		if($this->getRequest()->getParam('wd_infinite_loop')):
			$latestpost->setData('wd_infinite_loop',$this->getRequest()->getParam('wd_infinite_loop'));
		endif;
		if($this->getRequest()->getParam('wd_scrollbar')):
			$latestpost->setData('wd_scrollbar',$this->getRequest()->getParam('wd_scrollbar'));
		endif;
		if($this->getRequest()->getParam('wd_grap_cursor')):
			$latestpost->setData('wd_grap_cursor',$this->getRequest()->getParam('wd_grap_cursor'));
		endif;
		if($this->getRequest()->getParam('wd_sort_by')):
			$latestpost->setData('wd_sort_by',$this->getRequest()->getParam('wd_sort_by'));
		endif;
		if($this->getRequest()->getParam('wd_category')):
			$latestpost->setData('wd_category',$this->getRequest()->getParam('wd_category'));
		endif;
		
		if($this->getRequest()->getParam('wd_comment_count')):
			$latestpost->setData('wd_comment_count',$this->getRequest()->getParam('wd_comment_count'));
		endif;
		if($this->getRequest()->getParam('wd_tags')):
			$latestpost->setData('wd_tags',$this->getRequest()->getParam('wd_tags'));
		endif;
		if($this->getRequest()->getParam('wd_author')):
			$latestpost->setData('wd_author',$this->getRequest()->getParam('wd_author'));
		endif;
		if($this->getRequest()->getParam('wd_add_this')):
			$latestpost->setData('wd_add_this',$this->getRequest()->getParam('wd_add_this'));
		endif;
		if($this->getRequest()->getParam('wd_post_readmore')):
			$latestpost->setData('wd_post_readmore',$this->getRequest()->getParam('wd_post_readmore'));
		endif;
		if($this->getRequest()->getParam('wd_post_show_view_all')):
			$latestpost->setData('wd_post_show_view_all',$this->getRequest()->getParam('wd_post_show_view_all'));
		endif;
		if($this->getRequest()->getParam('store_id')):
			$latestpost->setData('store_id',$this->getRequest()->getParam('store_id'));
		endif;
		if($this->getRequest()->getParam('wd_bgimage')):
			$latestpost->setData('wd_bgimage',$this->getRequest()->getParam('wd_bgimage'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
			$latestpost->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;			
		if($this->getRequest()->getParam('wd_items_per_row')):
			$latestpost->setData('wd_items_per_row',$this->getRequest()->getParam('wd_items_per_row'));
		endif;			
		if($this->getRequest()->getParam('wd_post_content')):
			$latestpost->setData('wd_post_content',$this->getRequest()->getParam('wd_post_content'));
		endif;			
		if($this->getRequest()->getParam('wd_post_view_all_text')):
			$latestpost->setData('wd_post_view_all_text',$this->getRequest()->getParam('wd_post_view_all_text'));
		endif;			
		if($this->getRequest()->getParam('wd_post_show_view_all_url')):
			$latestpost->setData('wd_post_show_view_all_url',$this->getRequest()->getParam('wd_post_show_view_all_url'));
		endif;			
		if($this->getRequest()->getParam('wd_post_ids')):
			$latestpost->setData('wd_post_ids',$this->getRequest()->getParam('wd_post_ids'));
		endif;				
		
		
		$block = $latestpost->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}