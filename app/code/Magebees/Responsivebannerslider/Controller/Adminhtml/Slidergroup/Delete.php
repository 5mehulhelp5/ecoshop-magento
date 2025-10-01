<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
class Delete extends \Magento\Backend\App\Action
{
    public function execute()
    {
		$bannerId = $this->getRequest()->getParam('id');
		$slide_collection = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Slide')->getCollection();
		$slide_collection->addFieldToFilter('group_names', array(array('finset' => $bannerId)));	
		$slidercount = count($slide_collection->getData());
		if($slidercount <= 0){
			try {
				$banner = $this->_objectManager->get('Magebees\Responsivebannerslider\Model\Responsivebannerslider')->load($bannerId);
				$banner->delete();
				
				$store_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Store');
				$store_data = $store_model->getCollection()->addFieldToFilter('slidergroup_id',$bannerId); 
				$store_data->walk('delete');  
			
				$page_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Pages');
				$page_data = $page_model->getCollection()->addFieldToFilter('slidergroup_id',$bannerId); 
				$page_data->walk('delete');  
			
				$cate_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Categories');
				$cate_data = $cate_model->getCollection()->addFieldToFilter('slidergroup_id',$bannerId); 
				$cate_data->walk('delete');  
		
				$product_model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Product');
				$prd_data = $product_model->getCollection()->addFieldToFilter('slidergroup_id',$bannerId); 
				$prd_data->walk('delete');  
							
				$this->messageManager->addSuccess(
					__('Group was successfully deleted !')
				);
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}else{
			$this->messageManager->addError(
						__('Please Remove Assigned slider form the selected group before delete group.'));
			$this->_redirect('*/*/edit', array('id' => $bannerId));
		}
	}
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
