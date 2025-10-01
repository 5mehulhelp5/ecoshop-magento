<?php
namespace Magebees\MPM\Block;
class Mpm extends \Magento\Framework\View\Element\Template
{
  	protected $_helper;
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
    	\Magebees\MPM\Helper\Data $helper
    ) {     
        $this->_helper = $helper;
        parent::__construct($context);
    }
	public function getStatus(){
  		return $this->_helper->getStatus();
	}
	public function getPosition(){
		return $this->_helper->getPosition();
	}
	public function getMsgwidth(){
  		return $this->_helper->getMsgwidth();
	}
	public function getCloseoptions(){
  		return $this->_helper->getCloseoptions();
	}
	public function getSuccessbgcolor(){
  		return $this->_helper->getSuccessbgcolor();
	}
	public function getSuccessforecolor(){
  		return $this->_helper->getSuccessforecolor();
	}
	public function getErrorbgcolor(){
  		return $this->_helper->getErrorbgcolor();
	}
	public function getErrorforecolor(){
  		return $this->_helper->getErrorforecolor();
	}
	public function getNoticebgcolor(){
  		return $this->_helper->getNoticebgcolor();
	}
	public function getNoticeforecolor(){
  		return $this->_helper->getNoticeforecolor();
	}
	public function getWarningbgcolor(){
  		return $this->_helper->getWarningbgcolor();
	}
	public function getWarningforecolor(){
  		return $this->_helper->getWarningforecolor();
	}
	public function getSuccessfontsize(){
  		return $this->_helper->getSuccessfontsize();
	}
	public function getErrorfontsize(){
  		return $this->_helper->getErrorfontsize();
	}
	public function getNoticefontsize(){
  		return $this->_helper->getNoticefontsize();
	}
	public function getWarningfontsize(){
  		return $this->_helper->getWarningfontsize();
	}
	public function getSuccesspadding(){
  		return $this->_helper->getSuccesspadding();
	}
	public function getErrorpadding(){
  		return $this->_helper->getErrorpadding();
	}
	public function getNoticepadding(){
  		return $this->_helper->getNoticepadding();
	}
	public function getWarningpadding(){
  		return $this->_helper->getWarningpadding();
	}
	public function getMbautotime(){
  		return $this->_helper->getMbautotime();
	}
	public function getPopuptype(){
  		return $this->_helper->getPopuptype();
	}
}