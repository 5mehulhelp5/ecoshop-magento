<?php
namespace Magebees\Layerednavigation\Controller\Error;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
        protected $brands;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebees\Layerednavigation\Model\Brands $brands
    ) {
        parent::__construct($context);
        $this->brands = $brands;
    }
    public function execute()
    {
		 if (!$this->getRequest()->isAjax()) {
        $redirect_url= $this->getRequest()->getParam('redirect_url');		
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$this->messageManager->addError(__('Something went wrong when applying this filter, please contact administrator of this website.'));  
		$resultRedirect->setUrl($redirect_url);
       return $resultRedirect;
		 }
    }
}
