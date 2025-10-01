<?php
namespace Magebees\Finder\Controller;

/**
 * Finder Custom router Controller Router
 *
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory
    ) {
        $this->actionFactory = $actionFactory;
    }
    
    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pageId = trim($request->getPathInfo(), '/');
        if (strpos($pageId, 'finder') !== false) { // for rewrite custom finder page
            $request->setModuleName('finder')
                    ->setControllerName('index')
                    ->setActionName('index');
            $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        } else {
            return false;
        }
    }
}
