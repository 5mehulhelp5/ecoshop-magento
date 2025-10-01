<?php
namespace Magebees\PocoBase\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Content extends Action
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
		
		
		$content = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		
		if($this->getRequest()->getParam('wd_spacing')):
		$content->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$content->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
		$content->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;
		if($this->getRequest()->getParam('wd_content_position')):
		$content->setData('wd_content_position',$this->getRequest()->getParam('wd_content_position'));
		endif;
		if($this->getRequest()->getParam('wd_content_type')):
		$content->setData('wd_content_type',$this->getRequest()->getParam('wd_content_type'));
		endif;
		
		if($this->getRequest()->getParam('wd_block')):
		$content->setData('wd_block',$this->getRequest()->getParam('wd_block'));
		endif;
		
		if($this->getRequest()->getParam('wd_title')):
		$content->setData('wd_title',$this->getRequest()->getParam('wd_title'));
		endif;
		
		if($this->getRequest()->getParam('wd_description')):
		$content->setData('wd_description',$this->getRequest()->getParam('wd_description'));
		endif;
		if($this->getRequest()->getParam('wd_view_more')):
		$content->setData('wd_view_more',$this->getRequest()->getParam('wd_view_more'));
		endif;
		if($this->getRequest()->getParam('wd_view_more_text')):
		$content->setData('wd_view_more_text',$this->getRequest()->getParam('wd_view_more_text'));
		endif;
		if($this->getRequest()->getParam('wd_view_more_link')):
		$content->setData('wd_view_more_link',$this->getRequest()->getParam('wd_view_more_link'));
		endif;
		if($this->getRequest()->getParam('wd_additional_content')):
		$content->setData('wd_additional_content',$this->getRequest()->getParam('wd_additional_content'));
		endif;
		if($this->getRequest()->getParam('wd_additional_content_type')):
		$content->setData('wd_additional_content_type',$this->getRequest()->getParam('wd_additional_content_type'));
		endif;
		if($this->getRequest()->getParam('wd_additional_block')):
		$content->setData('wd_additional_block',$this->getRequest()->getParam('wd_additional_block'));
		endif;
		if($this->getRequest()->getParam('wd_image_section')):
		$content->setData('wd_image_section',$this->getRequest()->getParam('wd_image_section'));
		endif;
		if($this->getRequest()->getParam('wd_video_on_text')):
		$content->setData('wd_video_on_text',$this->getRequest()->getParam('wd_video_on_text'));
		endif;
		if($this->getRequest()->getParam('wd_video_on_popup')):
		$content->setData('wd_video_on_popup',$this->getRequest()->getParam('wd_video_on_popup'));
		endif;
		if($this->getRequest()->getParam('wd_video_url')):
		$content->setData('wd_video_url',$this->getRequest()->getParam('wd_video_url'));
		endif;
		if($this->getRequest()->getParam('store_id')):
		$content->setData('store_id',$this->getRequest()->getParam('store_id'));
		endif;
		
		if($this->getRequest()->getParam('template')):
			$content->setTemplate($this->getRequest()->getParam('template'));
		endif;
		$block = $content->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}