<?php
namespace Magebees\Finder\Controller\Index;

use \Magento\Framework\App\Action\Action;

class Search extends Action
{
	protected $_url;
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_url = $context->getUrl();
    }
    
    public function execute()
    {
        
        $finderid = $this->getRequest()->getParam('finder_id');
        if ($this->getRequest()->getParam('reset')) {
             $session = $this->_objectManager->create('Magento\Catalog\Model\Session');
            $findername    = 'mbfinder_' . $finderid;
            $session->setData($findername, "");
            $result_page_url =  $this->_url->getUrl('finder').$finderid;
        } else {
            $finderModel = $this->_objectManager->create('Magebees\Finder\Model\Finder')->setId($finderid);

            $dropdowns = $this->getRequest()->getParam('finder');
            if ($dropdowns) {
                $finderModel->saveDropDownValues($dropdowns);
            }

            $result_page_url =  $this->_url->getUrl('finder').$finderid.'/'.$this->getResultPageUrl($finderid);
            if ($this->getRequest()->getParam('category_id')) {
                $result_page_url =  $result_page_url."?cat=".$this->getRequest()->getParam('category_id');
            }
        }
        $this->getResponse()->setRedirect($result_page_url);
    }
    
    public function getResultPageUrl($finderid)
    {
        $urlParam = '';
        //$separator = Mage::getStoreConfig('finder_section/finder_group/separator');
        $separator = "-";
         $session = $this->_objectManager->create('Magento\Catalog\Model\Session');
        $name    = 'mbfinder_' . $finderid;
        
        $values = $session->getData($name);
        if (!is_array($values)) {
            $values = [];
        }
        
        foreach ($values as $key => $value) {
            if ('current' == $key) {
                $urlParam .= $value;
                break;
            }
           
            if (!empty($value) && is_numeric($key)) {
                $valueModel = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue')->load($value);
                if ($valueModel->getId()) {
                    $urlParam .= strtolower(preg_replace('/[^\da-zA-Z]/', $separator, $valueModel->getValue())).$separator;
                }
            }
        }
        return $urlParam;
    }
}
