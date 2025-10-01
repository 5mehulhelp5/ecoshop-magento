<?php
namespace Magebees\Layerednavigation\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Compare implements ObserverInterface
{

	protected $_scopeConfig;
	protected $responseFactory;
	protected $url;
	protected $eventManager;
	protected $httpRequest;
    public function __construct(
          \Magento\Framework\App\Request\Http $httpRequest,      
		  \Magento\Framework\App\ResponseFactory $responseFactory,
  		  EventManager $eventManager,
          \Magento\Framework\UrlInterface $url
    ) {
     	$this->responseFactory = $responseFactory;
        $this->url = $url;
		$this->eventManager = $eventManager;
        $this->httpRequest = $httpRequest;
       
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
		if(!$this->httpRequest->isAjax()):
			$url = $this->url->getCurrentUrl();
			$this->responseFactory->create()->setRedirect($url)->sendResponse();
		
				$this->eventManager->dispatch(
			'controller_action_predispatch',
			[ 'request' => $url ] //if request is available...
		);
        return $this;
		endif;
	}
}
