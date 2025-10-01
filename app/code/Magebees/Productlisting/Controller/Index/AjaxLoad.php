<?php
namespace Magebees\Productlisting\Controller\Index;
 
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
        $param_list_id = $this->getRequest()->getParam('listing_id'); 
        $wd_load_ajax = $this->getRequest()->getParam('wd_load_ajax'); 
		
		
        $block = $resultPage->getLayout()
                ->createBlock('Magebees\Productlisting\Block\Productlisting')
                ->setTemplate('Magebees_Productlisting::product_listing.phtml')
                ->setData('listing_id',$param_list_id)
                ->setData('wd_load_ajax',$wd_load_ajax)
		        ->toHtml();
        $result->setData(['result' => $block]);
        return $result;
    }
}