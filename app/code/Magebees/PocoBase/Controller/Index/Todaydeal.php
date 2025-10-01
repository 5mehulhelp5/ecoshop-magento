<?php
namespace Magebees\PocoBase\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Todaydeal extends Action
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
		
		$todaydeal = $resultPage->getLayout()->createBlock('Magebees\TodayDealProducts\Block\Widget\DealProductsWidget');
		
		if($this->getRequest()->getParam('wd_spacing')):
		$todaydeal->setData('wd_spacing',$this->getRequest()->getParam('wd_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bottom_spacing')):
		$todaydeal->setData('wd_bottom_spacing',$this->getRequest()->getParam('wd_bottom_spacing'));
		endif;
		if($this->getRequest()->getParam('wd_bgcolor')):
		$todaydeal->setData('wd_bgcolor',$this->getRequest()->getParam('wd_bgcolor'));
		endif;
		if($this->getRequest()->getParam('wd_deal')):
		$todaydeal->setData('wd_deal',$this->getRequest()->getParam('wd_deal'));
		endif;
		if($this->getRequest()->getParam('wd_show_viewall')):
		$todaydeal->setData('wd_show_viewall',$this->getRequest()->getParam('wd_show_viewall'));
		endif;
		if($this->getRequest()->getParam('wd_viewall_txt')):
		$todaydeal->setData('wd_viewall_txt',$this->getRequest()->getParam('wd_viewall_txt'));
		endif;
		if($this->getRequest()->getParam('wd_viewall_url')):
		$todaydeal->setData('wd_viewall_url',$this->getRequest()->getParam('wd_viewall_url'));
		endif;
		if($this->getRequest()->getParam('template')):
			$todaydeal->setTemplate($this->getRequest()->getParam('template'));
		endif;
		$block = $todaydeal->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}