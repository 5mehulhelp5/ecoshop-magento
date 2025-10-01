<?php
namespace Magebees\Onepagecheckout\Controller\Index;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
 
class Checkvatnumber extends \Magento\Framework\App\Action\Action
{
 	protected $helper;
 	protected $resultJsonFactory;
 	protected $resultRawFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
    }
    public function execute()
    {
		$vatnumber = $this->helper->jsonDecode($this->getRequest()->getContent());
        $vatnumber = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vatnumber));
        $cc = substr($vatnumber, 0, 2);
        $vn = substr($vatnumber, 2);
        $client = new \SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
        if($client){
            $params = array('countryCode' => $cc, 'vatNumber' => $vn);
            try{
                $r = $client->checkVat($params);
                if($r->valid == true){
                    $successMsg = __("VAT-ID is valid");
					$resultJson = $this->resultJsonFactory->create();
        			return $resultJson->setData($successMsg);
                } else {
                    $unsuccessMsg = __("VAT-ID is NOT valid");
					$resultJson = $this->resultJsonFactory->create();
        			return $resultJson->setData($unsuccessMsg);
                }
            } catch(\SoapFault $e) {
                $errorMsg = __("Error: ").$e->faultstring;
				$resultJson = $this->resultJsonFactory->create();
        		return $resultJson->setData($errorMsg);
            }
        } else {
            $serverdownMsg = __("Connection to host not possible, europe.eu down?");
            $resultJson = $this->resultJsonFactory->create();
        	return $resultJson->setData($serverdownMsg);
        }
    }
}