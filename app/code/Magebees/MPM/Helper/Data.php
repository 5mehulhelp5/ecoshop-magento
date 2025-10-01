<?php
namespace Magebees\MPM\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   	protected $_scopeConfig;
    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {     
		$this->_scopeConfig = $scopeConfig;
   	}  
	public function getStatus(){
  		return $this->_scopeConfig->getValue('mpm/general/mpmenable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getPosition(){
  		return $this->_scopeConfig->getValue('mpm/general/mpmposition', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getMsgwidth(){
  		return $this->_scopeConfig->getValue('mpm/general/msgwidth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getCloseoptions(){
  		return $this->_scopeConfig->getValue('mpm/general/closeoptions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getSuccessbgcolor(){
  		return $this->_scopeConfig->getValue('mpm/general/successbgcolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getSuccessforecolor(){
  		return $this->_scopeConfig->getValue('mpm/general/successforecolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getErrorbgcolor(){
  		return $this->_scopeConfig->getValue('mpm/general/errorbgcolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getErrorforecolor(){
  		return $this->_scopeConfig->getValue('mpm/general/errorforecolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getNoticebgcolor(){
  		return $this->_scopeConfig->getValue('mpm/general/noticebgcolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}	

	public function getNoticeforecolor(){
  		return $this->_scopeConfig->getValue('mpm/general/noticeforecolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getWarningbgcolor(){
  		return $this->_scopeConfig->getValue('mpm/general/warningbgcolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getWarningforecolor(){
  		return $this->_scopeConfig->getValue('mpm/general/warningforecolor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getSuccessfontsize(){
  		return $this->_scopeConfig->getValue('mpm/general/successfontsize', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getErrorfontsize(){
  		return $this->_scopeConfig->getValue('mpm/general/errorfontsize', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getNoticefontsize(){
  		return $this->_scopeConfig->getValue('mpm/general/noticefontsize', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getWarningfontsize(){
  		return $this->_scopeConfig->getValue('mpm/general/warningfontsize', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getSuccesspadding(){
  		return $this->_scopeConfig->getValue('mpm/general/successpadding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getErrorpadding(){
  		return $this->_scopeConfig->getValue('mpm/general/errorpadding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getNoticepadding(){
  		return $this->_scopeConfig->getValue('mpm/general/noticepadding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getWarningpadding(){
  		return $this->_scopeConfig->getValue('mpm/general/warningpadding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getMbautotime(){
  		return $this->_scopeConfig->getValue('mpm/general/mbautotime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getPopuptype(){
  		return $this->_scopeConfig->getValue('mpm/general/mpmpopuptype', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}