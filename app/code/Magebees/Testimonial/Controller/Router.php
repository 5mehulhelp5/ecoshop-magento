<?php
namespace Magebees\Testimonial\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $is_enable = $this->_scopeConfig->getValue('testimonial/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $custom_url_key = $this->_scopeConfig->getValue('testimonial/frontend_settings/url_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $identifier = trim($request->getPathInfo(), '/');
        
        if ($is_enable) {
            if (strpos($identifier, 'testimonial') !== false) {
                $request->setModuleName('testimonial')->setControllerName('index')->setActionName('index');
            } elseif (strpos($identifier, $custom_url_key) !== false) {
                $request->setModuleName('testimonial')->setControllerName('index')->setActionName('index');
            } else {
                //There is no match
                return;
            }
        } else {
            return;
        }

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
