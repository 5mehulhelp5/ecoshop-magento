<?php

namespace Magebees\Testimonial\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;

class Index extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_ENABLED = 'testimonial/setting/enable';
    protected $scopeConfig;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            throw new NotFoundException(__('Page not found.'));
        }
        return parent::dispatch($request);
    }
    
    public function execute()
    {
        $testimonialConfig=$this->scopeConfig->getValue('testimonial/frontend_settings', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $title=$testimonialConfig['title_list_testimonial'];
        $this->_view->getPage()->getConfig()->getTitle()->set($title);
        return $this->resultPageFactory->create();
    }
}
