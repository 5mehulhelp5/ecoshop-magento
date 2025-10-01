<?php
namespace Magebees\PocoBase\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Newsletter extends Action
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
		
		$newsletter = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		if($this->getRequest()->getParam('wd_enable')):
		$newsletter->setData('wd_enable',$this->getRequest()->getParam('wd_enable'));
		endif;
		if($this->getRequest()->getParam('wd_spacing')):
		$newsletter->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$newsletter->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_shopnow')):
		$newsletter->setData('wd_shopnow',$this->getRequest()->getParam('wd_shopnow'));
		endif;
		if($this->getRequest()->getParam('wd_bgimage')):
		$newsletter->setData('wd_bgimage',$this->getRequest()->getParam('wd_bgimage'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
		$newsletter->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;
		if($this->getRequest()->getParam('wd_show_heading')):
		$newsletter->setData('wd_show_heading',$this->getRequest()->getParam('wd_show_heading'));
		endif;
		
		if($this->getRequest()->getParam('wd_newsletter_title')):
		$newsletter->setData('wd_newsletter_title',$this->getRequest()->getParam('wd_newsletter_title'));
		endif;
		
		if($this->getRequest()->getParam('wd_heading_logo')):
		$newsletter->setData('wd_heading_logo',$this->getRequest()->getParam('wd_heading_logo'));
		endif;
		if($this->getRequest()->getParam('wd_newsletter_text_placeholder')):
		$newsletter->setData('wd_newsletter_text_placeholder',$this->getRequest()->getParam('wd_newsletter_text_placeholder'));
		endif;
		if($this->getRequest()->getParam('wd_newsletter_text')):
		$newsletter->setData('wd_newsletter_text',$this->getRequest()->getParam('wd_newsletter_text'));
		endif;
		if($this->getRequest()->getParam('wd_newsletter_button_text')):
		$newsletter->setData('wd_newsletter_button_text',$this->getRequest()->getParam('wd_newsletter_button_text'));
		endif;
		if($this->getRequest()->getParam('template')):
			$newsletter->setTemplate($this->getRequest()->getParam('template'));
		endif;
		$block = $newsletter->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}