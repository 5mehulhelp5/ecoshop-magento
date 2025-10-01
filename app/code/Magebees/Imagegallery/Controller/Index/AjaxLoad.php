<?php
namespace Magebees\Imagegallery\Controller\Index;
 
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
		$imageGallery = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		if($this->getRequest()->getParam('wd_spacing')):
		$imageGallery->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$imageGallery->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_show_heading')):
		$imageGallery->setData('wd_show_heading',$this->getRequest()->getParam('wd_show_heading'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
		$imageGallery->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;
		if($this->getRequest()->getParam('wd_bgimage')):
		$imageGallery->setData('wd_bgimage',$this->getRequest()->getParam('wd_bgimage'));
		endif;
		if($this->getRequest()->getParam('wd_heading')):
		$imageGallery->setData('wd_heading',$this->getRequest()->getParam('wd_heading'));
		endif;
		
		if($this->getRequest()->getParam('wd_show_description')):
		$imageGallery->setData('wd_show_description',$this->getRequest()->getParam('wd_show_description'));
		endif;
		
		if($this->getRequest()->getParam('wd_description_position')):
		$imageGallery->setData('wd_description_position',$this->getRequest()->getParam('wd_description_position'));
		endif;
		if($this->getRequest()->getParam('wd_description')):
		$imageGallery->setData('wd_description',$this->getRequest()->getParam('wd_description'));
		endif;
		if($this->getRequest()->getParam('no_of_image')):
		$imageGallery->setData('no_of_image',$this->getRequest()->getParam('no_of_image'));
		endif;
		if($this->getRequest()->getParam('wd_slider')):
		$imageGallery->setData('wd_slider',$this->getRequest()->getParam('wd_slider'));
		endif;
		if($this->getRequest()->getParam('wd_items_per_slide')):
		$imageGallery->setData('wd_items_per_slide',$this->getRequest()->getParam('wd_items_per_slide'));
		endif;
		if($this->getRequest()->getParam('wd_items_per_row')):
		$imageGallery->setData('wd_items_per_row',$this->getRequest()->getParam('wd_items_per_row'));
		endif;
		if($this->getRequest()->getParam('wd_autoscroll')):
		$imageGallery->setData('wd_autoscroll',$this->getRequest()->getParam('wd_autoscroll'));
		endif;
		if($this->getRequest()->getParam('wd_auto_play_delaytime')):
		$imageGallery->setData('wd_auto_play_delaytime',$this->getRequest()->getParam('wd_auto_play_delaytime'));
		endif;
		if($this->getRequest()->getParam('wd_autoplayoff')):
		$imageGallery->setData('wd_autoplayoff',$this->getRequest()->getParam('wd_autoplayoff'));
		endif;
		if($this->getRequest()->getParam('wd_slide_auto_height')):
		$imageGallery->setData('wd_slide_auto_height',$this->getRequest()->getParam('wd_slide_auto_height'));
		endif;
		if($this->getRequest()->getParam('wd_navarrow')):
		$imageGallery->setData('wd_navarrow',$this->getRequest()->getParam('wd_navarrow'));
		endif;
		
		if($this->getRequest()->getParam('wd_pagination')):
		$imageGallery->setData('wd_pagination',$this->getRequest()->getParam('wd_pagination'));
		endif;
		if($this->getRequest()->getParam('wd_pagination_type')):
		$imageGallery->setData('wd_pagination_type',$this->getRequest()->getParam('wd_pagination_type'));
		endif;
		if($this->getRequest()->getParam('wd_infinite_loop')):
		$imageGallery->setData('wd_infinite_loop',$this->getRequest()->getParam('wd_infinite_loop'));
		endif;
		if($this->getRequest()->getParam('wd_scrollbar')):
		$imageGallery->setData('wd_scrollbar',$this->getRequest()->getParam('wd_scrollbar'));
		endif;
		if($this->getRequest()->getParam('wd_grap_cursor')):
		$imageGallery->setData('wd_grap_cursor',$this->getRequest()->getParam('wd_grap_cursor'));
		endif;
		
		if($this->getRequest()->getParam('store_id')):
		$imageGallery->setData('store_id',$this->getRequest()->getParam('store_id'));
		endif;
		if($this->getRequest()->getParam('template')):
			$imageGallery->setTemplate($this->getRequest()->getParam('template'));
		endif;
		$block = $imageGallery->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}