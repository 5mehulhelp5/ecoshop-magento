<?php

namespace Magebees\RichSnippets\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class Breadcrumbs extends Template
{
   
    protected $_template = 'breadcrumbs.phtml';
	protected $_scopeConfig;
	protected $_objectManager;
	public $registry;
	protected $_catalogData;
    public function __construct(
        Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Registry $registry,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->_catalogData = $catalogData;
        parent::__construct($context, $data);
    }

    public function canShowContent()
    {        
        $ext_enable=$this->_scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $breadcrumbs_enable=$this->_scopeConfig->getValue('richsnippets/breadcrumbs/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $breadcrumbs_type=$this->_scopeConfig->getValue('richsnippets/breadcrumbs/type',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         if ((!$breadcrumbs_enable)||(!$ext_enable)) {
            return false;
        }
      
        $breadcrumbs = $this->_objectManager->get('Magebees\RichSnippets\Plugin\Breadcrumbs');
        if ($breadcrumbs->crumbs) {   
        $breadcrumbs_arr=$breadcrumbs->crumbs;
        end($breadcrumbs_arr);
        $short_breadcrumb_arr_list=array();
        $short_breadcrumb_arr=prev($breadcrumbs_arr); 
        if($breadcrumbs_type==1)
        {
            $short_breadcrumb_arr_list[]=$short_breadcrumb_arr;
            return $short_breadcrumb_arr_list;  
        }  
        else
        {
             return $breadcrumbs_arr; 
        }   
        }

        return false;
    }
    public function canShowProductBreadcrumb()
    {
         $ext_enable=$this->_scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $breadcrumbs_enable=$this->_scopeConfig->getValue('richsnippets/breadcrumbs/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $breadcrumbs_type=$this->_scopeConfig->getValue('richsnippets/breadcrumbs/type',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         if ((!$breadcrumbs_enable)||(!$ext_enable)) {
            return false;
        }
         $evercrumbs = [];
        
        $evercrumbs[] = [
            'label' => 'Home',
            'title' => 'Go to Home Page',
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        ];

        $path = $this->_catalogData->getBreadcrumbPath();
        $product = $this->registry->registry('current_product');
        $categoryCollection = clone $product->getCategoryCollection();
        $categoryCollection->clear();
        $categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)->addAttributeToFilter('path', ['like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"]);
        $categoryCollection->setPageSize(1);
        $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
        foreach ($breadcrumbCategories as $category) {
            $evercrumbs[] = [
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link' => $category->getUrl()
            ];
        }
    
        
        $evercrumbs[] = [
                'label' => $product->getName(),
                'title' => $product->getName(),
                'link' => ''
            ];

        if ($evercrumbs) {   
        $breadcrumbs_arr=$evercrumbs;
        end($breadcrumbs_arr);
        $short_breadcrumb_arr_list=array();
        $short_breadcrumb_arr=prev($breadcrumbs_arr); 
        if($breadcrumbs_type==1)
        {
            $short_breadcrumb_arr_list[]=$short_breadcrumb_arr;
            return $short_breadcrumb_arr_list;  
        }  
        else
        {
             return $breadcrumbs_arr; 
        }   
        }       
        return false;
    }
}
