<?php
namespace Magebees\Onepagecheckout\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
	protected $scopeConfig;
	protected $_customerSession;
	protected $serialize;
	protected $objectManager;
	protected $_json;
    
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Serialize\Serializer\Json $json,
		ObjectManagerInterface $objectManager
    ) {
		$this->_storeManager = $storeManager;
		$this->serialize = $serialize;
		$this->_customerSession = $customerSession;
		$this->_json = $json;
		$this->objectManager = $objectManager;
        parent::__construct($context);
	}
	public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }
    public function mbserialize($data){
		return $this->serialize->serialize($data);
	}
	public function mbunserialize($data){
		return $this->serialize->unserialize($data);
	}
	public function isEnabled()
    {
        return $this->scopeConfig->getValue('magebees_Onepagecheckout/orderattachment/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getOrdercomment()
    {
		$Ordercomment = 1;
        return $Ordercomment;
    }
    public function getOrdercommenttitle()
    {
        $Ordercommenttitle = $this->scopeConfig
        ->getValue('magebees_Onepagecheckout/orderattachment/order_comment_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Ordercommenttitle;
    }
    public function getOrderfileupload()
    {
		$Order_file_upload = 1;
        return $Order_file_upload;
    }
    public function getOrderfileuploadstatus()
    {
        $checkoutSession = $this->objectManager->create('\Magento\Checkout\Model\Session');
        if ($checkoutSession->getFileuploadstatus()) {
            $Order_file_upload_status = $checkoutSession->getFileuploadstatus();
        } else {
            $Order_file_upload_status = 0;
        }
        return $Order_file_upload_status;
    }
    public function getOrdercommentsstatus()
    {
        $checkoutSession = $this->objectManager->create('\Magento\Checkout\Model\Session');

        if ($checkoutSession->getOrdercommentsstatus()) {
            $Order_comments_status = $checkoutSession->getOrdercommentsstatus();
        } else {
            $Order_comments_status = 0;
        }
        return $Order_comments_status;
    }
    public function getOrderfileuploadvalue()
    {
        $checkoutSession = $this->objectManager->create('\Magento\Checkout\Model\Session');

        if ($checkoutSession->getFileuploadvalue()) {
            $Order_file_upload_value = $checkoutSession->getFileuploadvalue();
        } else {
            $Order_file_upload_value = "";
        }
        return $Order_file_upload_value;
    }
    public function getOrdercommenttexttitle()
    {
        $Ordercommenttexttitle = $this->scopeConfig
        ->getValue('magebees_Onepagecheckout/orderattachment/order_comment_text_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Ordercommenttexttitle;
    }
    public function getOrderfiletexttitle()
    {
        $Orderfiletexttitle = $this->scopeConfig
        ->getValue('magebees_Onepagecheckout/orderattachment/order_comment_file_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Orderfiletexttitle;
    }
    public function getBaseurlordercomment()
    {
        $Baseurl =  $this->_storeManager->getStore()->getBaseUrl().'onepage/ordercomment/index';
        return $Baseurl;
    }
    public function getOrdercommentdelete()
    {
        $Baseurl =  $this->_storeManager->getStore()->getBaseUrl().'onepage/Ordercommentdelete/Index';
        return $Baseurl;
    }
    public function getOrderfiledelete()
    {
        $Baseurl =  $this->_storeManager->getStore()->getBaseUrl().'onepage/Orderfiledelete/Index';
        return $Baseurl;
    }
    public function getOrdercommentField()
    {
	   $getOrdercommentField = 0;
	   return $getOrdercommentField;
    }
    public function getOrdercommentFile()
    {
		$getOrdercommentFile = 0;
        return $getOrdercommentFile;
    }
    public function getOrdercommentFiletype()
    {
        $getOrdercommentFiletype = $this->scopeConfig
        ->getValue('magebees_Onepagecheckout/orderattachment/order_comments_file_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $getOrdercommentFiletype;
    }
	public function getNumberofAttachment()
    {
        $getNumberofAttachment = $this->scopeConfig
        ->getValue('magebees_Onepagecheckout/orderattachment/numberofattachment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $getNumberofAttachment;
    }
	public function getJsonEncode($data)
	{
		return $this->_json->serialize($data);
	}
	public function getJsonDecode($data)
	{
		return $this->_json->unserialize($data);
	}
	public function getObjectManager()
    {
        return $this->objectManager;
    }
	public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}