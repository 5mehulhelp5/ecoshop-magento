<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $data = $this->getRequest()->getPost()->toarray();
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$jsHelper = $om->get('Magento\Backend\Helper\Js');
	
		if ($data) {
			if(isset($data['links']['slider'])){
				$data['in_products'] = $jsHelper->decodeGridSerializedInput($data['links']['slider']);
			}
            $model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Responsivebannerslider');
			
			$CriticalCss = $this->_objectManager->create('Magebees\PocoThemes\Model\CriticalCss');
          	$id = $this->getRequest()->getParam('slidergroup_id');
	        if ($id) {
                $model->load($id);
			}
			
			$model->setData($data);
    
            try {
				$title = $model->getTitle();
				$unique_code =  str_replace(" ","-",strtolower($title));
				$group_unique_code = $model->getUniqueCode();
				if($group_unique_code != $unique_code)
				{
					$model->setUniqueCode($unique_code);
				}
				
				
                $model->save();
				
				$store_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Store');
				if($id != "") {			
					$store_data = $store_model->getCollection()
									->addFieldToFilter('slidergroup_id',$id); 
					$store_data->walk('delete');  
				}
				
				if (in_array("0", $model->getData('store_id')))	{
					$allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
					foreach ($allStores as $_eachStoreId => $val) 	{
						$_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId)->getId();
						$data_store['slidergroup_id'] = $model->getData('slidergroup_id');
						$data_store['store_id'] = $_storeId;
						$store_model->setData($data_store);
						$store_model->save();
					}
				}else{
					
					foreach($model->getData('store_id') as $store){
						$data_store['slidergroup_id'] = $model->getData('slidergroup_id');
						$data_store['store_id'] = $store;
						$store_model->setData($data_store);
						$store_model->save();
					} 
			
				}
				
				$page_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Pages');
				if($id != "") {			
					$page_data = $page_model->getCollection()
									->addFieldToFilter('slidergroup_id',$id); 
					$page_data->walk('delete');  
				}
				$cmspages = $model->getData('pages');
				if(isset($cmspages)) {
					foreach($model->getData('pages') as $pages)	{
						$data_page['slidergroup_id'] = $model->getData('slidergroup_id');
						$data_page['pages'] = $pages;
						$page_model->setData($data_page);
						$page_model->save();
					} 
				}

				$cate_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Categories');
				if($id != "") {			
					$cate_data = $cate_model->getCollection()
									->addFieldToFilter('slidergroup_id',$id); 
					$cate_data->walk('delete');  
				}
				$catedata = $model->getData('categories');
				if(isset($catedata))  {
					foreach($model->getData('categories') as $category_id)	{
						if($category_id != "") {
							$data_cate['slidergroup_id'] = $model->getData('slidergroup_id');
							$data_cate['category_ids'] = $category_id;
							$cate_model->setData($data_cate);
							$cate_model->save();
						}
					} 
				}	

				$in_products = $model->getData('in_products');
							
				if($in_products != "") 	{
					$product_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Product');
					if($id != "") {			
						$prd_data = $product_model->getCollection()->addFieldToFilter('slidergroup_id',$id); 
						$prd_data->walk('delete');  
					}
					
					foreach($model->getData('in_products') as $product)	{
						$data_prd['slidergroup_id'] = $model->getData('slidergroup_id');
						$data_prd['product_sku'] = $product;
						$product_model->setData($data_prd);
						$product_model->save();
					} 
				}				
				
				//$reader = $this->_objectManager->get('Magento\Framework\Module\Dir\Reader');
				//$moduleviewDir=$reader->getModuleDir('view', 'Magebees_Responsivebannerslider');
				$cssDir= '/pub/media/responsivebannerslider';

				$cssDir = $this->_objectManager->create('Magebees\Responsivebannerslider\Helper\Data')->getStaticCssDirectoryPath($cssDir);
 
				$csschecker = $this->_objectManager->create('Magento\Framework\Filesystem\Driver\File');

				$dir_permission = 0755;
				$file_permission = 0644; 
				
				if(!$csschecker->isExists($cssDir))
				{
					$csschecker->createDirectory($cssDir,$permissions = 0777);
				}
				if(!$csschecker->isWritable($cssDir))
				{
					$csschecker->changePermissionsRecursively($cssDir,$dir_permission,$file_permission);
				}


				$path = $cssDir.'/';
				$path .= "group-".$model->getData('slidergroup_id').".css";
				$css = $this->get_menu_css($model->getData('slidergroup_id'));
				
				$unique_code = $model->getData('unique_code');
				$criticalCssCollection = $CriticalCss->getCollection()
								 ->addFieldToFilter('stores', $unique_code)
								 ->addFieldToFilter('type', 'slider_group');
								 if($criticalCssCollection->getSize()>0):
									 $criticalCssCollection->walk('delete');
								 endif;
								 $criticalCssInfo = array();
								 $criticalCssInfo['stores'] = $unique_code;
								 $criticalCssInfo['type'] = 'slider_group';
								 $criticalCssInfo['css'] = $css;
								 $CriticalCss->setData($criticalCssInfo)->save();
				file_put_contents($path,$css);
		
                $this->messageManager->addSuccess(__('Group was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getSlidergroupId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('slidergroup_id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
		
	public function get_menu_css($group_id){
		$groupdata = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Responsivebannerslider')->load($group_id);
		$max_width = $groupdata->getMaxWidth();
		$content_background = $groupdata->getContentBackground();
		$banner_content_tcolor = $groupdata->getBannerContentTcolor();
		$content_opacity = $groupdata->getContentOpacity();
		$navigation_acolor = $groupdata->getNavigationAcolor();
		$pagination_color = $groupdata->getPaginationColor();
		$pagination_bocolor = $groupdata->getPaginationBocolor();
		$pagination_active_color = $groupdata->getPaginationActive();
		$pagination_bar = $groupdata->getPaginationBar();
		$pagination_bocolor = $groupdata->getPaginationBocolor();
		if ($max_width > 0) {
			$max_width = $groupdata->getMaxWidth().'px';
		} else {
			$max_width = "";
		}
		$css = '';
		$css .= '#cwsslider-'.$group_id.' { }';
		$css .= '#cwsslider-'.$group_id.' { max-width:'.$max_width.'; }';
		$css .= '#cwsslider-'.$group_id.' .sliderdecs { background-color:'.$content_background.'; opacity:0.'.$content_opacity.'; color:'.$banner_content_tcolor.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-arw > div:before { color:'.$navigation_acolor.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet { background-color:'.$pagination_color.' !important; border-color:'.$pagination_bocolor.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-progressbar .swiper-pagination-progressbar-fill { background-color:'.$pagination_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet.swiper-pagination-bullet-active { background-color:'.$pagination_active_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager .swiper-pagination-bullet:hover { background-color:'.$pagination_active_color.' !important; }';
		$css .= '#cwsslider-'.$group_id.' .cws-arw > div { background-color:'.$pagination_bar.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-fraction { color:'.$pagination_color.'; }';
		$css .= '#cwsslider-'.$group_id.' .cws-pager.swiper-pagination-fraction .swiper-pagination-current { color:'.$pagination_active_color.'; }';
		return $css;
	}
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
