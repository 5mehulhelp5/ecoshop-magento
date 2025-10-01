<?php
namespace Magebees\PocoBase\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Advertisement extends Action
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
		$params = $this->getRequest()->getParams();
		
		$advertisementblock = $resultPage->getLayout()->createBlock('Magebees\Advertisementblock\Block\Widget\Advertisementwidget');
		
		$advertisementblock = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
		
		 if($this->getRequest()->getParam('enabled')):
		$advertisementblock->setData('enabled',$this->getRequest()->getParam('enabled'));
		endif;
		if($this->getRequest()->getParam('wd_spacing')):
		$advertisementblock->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$advertisementblock->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_custom_class')):
		$advertisementblock->setData('wd_custom_class',$this->getRequest()->getParam('wd_custom_class'));
		endif;
		if($this->getRequest()->getParam('wd_advertisement')):
		$advertisementblock->setData('wd_advertisement',$this->getRequest()->getParam('wd_advertisement'));
		endif;
		if($this->getRequest()->getParam('wd_style')):
		$advertisementblock->setData('wd_style',$this->getRequest()->getParam('wd_style'));
		endif;
		if($this->getRequest()->getParam('store_id')):
		$advertisementblock->setData('store_id',$this->getRequest()->getParam('store_id'));
		endif;
		if($this->getRequest()->getParam('title')):
		$advertisementblock->setData('title',$this->getRequest()->getParam('title'));
		$advertisementblock->setTitle($this->getRequest()->getParam('title'));
		endif;
		
		if($this->getRequest()->getParam('subtitle')):
		$advertisementblock->setSubtitle($this->getRequest()->getParam('title'));
		$advertisementblock->setData('subtitle',$this->getRequest()->getParam('subtitle'));
		endif;
		
		if($this->getRequest()->getParam('advertisement')):
		$advertisementblock->setData('advertisement',$this->getRequest()->getParam('advertisement'));
		endif;
		if($this->getRequest()->getParam('style')):
		$advertisementblock->setData('style',$this->getRequest()->getParam('style'));
		endif;
		
		if($this->getRequest()->getParam('template')):
			$advertisementblock->setTemplate('Magebees_Advertisementblock::widget/advertisement_ajax.phtml');
		endif;
		$block = $advertisementblock->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}