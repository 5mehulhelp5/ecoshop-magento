<?php

namespace Magebees\Layerednavigation\plugin;

class Layer

{

    	

	public function afterGetProductCollection(

    \Magento\Catalog\Model\Layer $subject,

    $result

  ) {

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$storeManager = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');    

		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

		

		$request = $objectManager->get('Magento\Framework\App\Request\Http');  

		$param = $request->getParams();		

		

	

		



	//	print_r($category->getData()); die; 



		if (array_key_exists("product_list_limit",$param)) {

			

		}else{

	        $size = $storeManager->getValue('catalog/frontend/grid_per_page', $storeScope);

			if($size){

				$result->setPageSize($size);

			}

		}	

		

		if (array_key_exists("product_list_order",$param)){

			//echo "d"; die;

  			$currentOrder = $param['product_list_order']; 

		}else{

			if (array_key_exists("id",$param)){	

				$categorysFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');

				$categoryId = $param['id']; // YOUR CATEGORY ID.

				$category = $categorysFactory->create()->load($categoryId);

				$category_data = $category->getData();		

				if(array_key_exists("default_sort_by", $category_data))

				{
					$currentOrder = $category->getData('default_sort_by');

				}else{
					 $currentOrder = 'position';

				}
			}else{
					 $currentOrder = 'position';

				}	

		}

		

		if (array_key_exists("product_list_dir",$param)){

  			$product_list_dir = $param['product_list_dir']; 

		}else{

			$product_list_dir = 'asc';

		}

		
		if(!$currentOrder){			$currentOrder = 'position';			}		
		$result->setOrder($currentOrder, $product_list_dir);



		if (array_key_exists("p",$param)){

			$result->setCurPage($param['p']);

		}

	 

		return $result;

  }

	





}