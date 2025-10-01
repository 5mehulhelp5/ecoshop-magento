<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class CustomerLogin implements ObserverInterface
{
    protected $scopeConfig;
    protected $_magebeesConfigHelper;
    protected $responseFactory;
    protected $_urlinterface;
    protected $_request;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Magento\Framework\UrlInterface $urlinterface,
        \Magento\Framework\App\ResponseFactory $responseFactory,
		\Magebees\Onepagecheckout\Helper\Configurations $magebeesConfigHelper,
		 \Magento\Framework\App\RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
    	$this->_urlinterface = $urlinterface;
		$this->_magebeesConfigHelper = $magebeesConfigHelper;
        $this->responseFactory = $responseFactory;
		$this->_request = $request;
    }

	public function execute(Observer $observer)
    {
		$current = $this->_request->getPost()->toarray();
		if(isset($current['magebees_ocp'])){
			$enabled = $this->_magebeesConfigHelper->getEnable();
			if ($enabled == 1) {
				if($current['magebees_ocp']){
					$resultRedirect = $this->responseFactory->create();
					$resultRedirect->setRedirect($this->_urlinterface->getUrl('checkout'))->sendResponse('200');
					return;
				}	
			}
		}
    }
}
