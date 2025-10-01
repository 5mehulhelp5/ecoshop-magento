<?php
namespace Magebees\Ajaxquickview\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Catalog\Controller\Product\View
{

    public function execute()
    {
        
        $isAjax = $this->getRequest()->isAjax();
        if ($isAjax) {
            $id= $this->getRequest()->getParam('id');
            if(!$id){
            $manager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$helper = $this->_objectManager->get('Magebees\Ajaxquickview\Helper\Data');
			$mage_version = $helper->getMagentoVersion();
	
            $store_id =  $manager->getStore()->getId();
            // get connnect pdo
            $_resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $conn =  $_resource->getConnection('core_read');
            $_path = $this->getRequest()->getParam('path') ? $this->getRequest()->getParam('path') : strstr($this->_request->getRequestUri(), '/path');
            $_path = str_replace("/path/", '', $_path);
            $_path = (strpos($_path, '?') !== false) ? substr($_path, strpos($_path, '?')) : $_path;
            // escape url path
			
			$url = $this->_request->getRequestUri();
			$_path = substr($url, strpos($url, '/path/'));
			$_path = str_replace("/path/", '', $_path);
			$path_arr = array();
			if(strpos($_path, '?') !== false){
				$path_arr = explode("?",(string)$_path);
				$_path = $path_arr[0];
			}
			
            $str = $conn->quote($_path);
            $url_rewrite = $_resource->getTableName('url_rewrite');
            $select =  $conn->select()
                ->from(['rp' => $url_rewrite], new \Zend_Db_Expr('entity_id'))
                ->where('rp.request_path in ('.$str.')')
                ->where('rp.store_id = ?', $store_id);
            $productId =  $conn->fetchOne($select);
            }
            else
            {
             $productId =$id;
            }
            
            if (!$productId) {
                return false;
            } else {
             
                 $this->getRequest()->setParam('id', $productId);
                 $product = $this->_initProduct();
                 $layout = $this->_objectManager->get('Magento\Framework\View\LayoutInterface');
                
                switch ($product->getTypeId()) {
    case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
	if(version_compare($mage_version, '2.3.2', '>='))
	{ 
							$layout->getUpdate()->load(['quickview_product_type_bundle_update_option']);
	}
    elseif((version_compare($mage_version, '2.3.0', '>'))&&(version_compare($mage_version, '2.3.2', '<')))
    { 
                            $layout->getUpdate()->load(['quickview_product_type_bundle_update']);
    }
	else
	{
		$layout->getUpdate()->load(['quickview_product_type_bundle']);
	}
	break;
                    
	case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:
if(version_compare($mage_version, '2.3.0', '>'))
{  
$layout->getUpdate()->load(['quickview_product_type_downloadable_update']);
}
else
{
	$layout->getUpdate()->load(['quickview_product_type_downloadable']);
}
break;
                    
case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
if(version_compare($mage_version, '2.3.0', '>'))
{  
 $layout->getUpdate()->load(['quickview_product_type_grouped_update']);
}
else
{
	 $layout->getUpdate()->load(['quickview_product_type_grouped']);
}
break;
                     
case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
if(version_compare($mage_version, '2.3.0', '>'))
{  
						$layout->getUpdate()->load(['quickview_product_type_simple_update']);
}
else
{
	$layout->getUpdate()->load(['quickview_product_type_simple']);
}
break;
                    
default:
if(version_compare($mage_version, '2.3.0', '>'))
{  
	
$layout->getUpdate()->load(['quickview_product_type_configurable_update']);
}
else
{
 $layout->getUpdate()->load(['quickview_product_type_configurable']);
}
                }
                
                 $product_info=$layout->getOutput();
                 $output=[];
                 $output['sucess']=true;
                 $output['type_product']=$product->getTypeId();
                 $output['title']=$product->getName();
                 $output['product_detail']=$product_info;
                // $output = ['sucess' => true,'type_product' => $product->getTypeId(), 'title' => $product->getName(),'product_detail' => $product_info];
                return $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($output));
            }
        } else {
			
			$params=$this->getRequest()->getParams();
			$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
			$baseurl=$storeManager->getStore()->getBaseUrl();
			if(isset($params['path']))
			{
				$path=$params['path'];
				$redirecturl=$baseurl.$path;
				 return $this->getResponse()->setRedirect($redirecturl);
			}
            return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}
