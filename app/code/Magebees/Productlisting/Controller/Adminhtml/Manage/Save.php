<?php
namespace Magebees\Productlisting\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
	
    protected $_jsHelper;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        parent::__construct($context);
        $this->_jsHelper = $jsHelper;
    }
	
	public function execute()
    {
		$data = $this->getRequest()->getPost()->toArray();
		if($data){
			$id = $this->getRequest()->getParam('listing_id');
			if (isset($data['links'])) {
				$data['products'] = $this->_jsHelper->decodeGridSerializedInput($data['links']['products']);
			} else {
				$data['products'] = $this->_objectManager->create('Magebees\Productlisting\Model\SelectProduct')->getCollection()
				->addFieldToFilter('listing_id', ['eq' => $id])->getColumnValues('product_id');
			}
			
			$model = $this->_objectManager->create('Magebees\Productlisting\Model\Productlisting');
			if ($id) {
                $model->load($id);
            }
			try {
				
				if (isset($data['stores']) && is_array($data['stores'])) {
					$data['stores'] = array_unique($data['stores']);
					$data['stores'] = implode(',', $data['stores']);
				}
				if (isset($data['category_ids']) && is_array($data['category_ids'])) {
					$data['category_ids'] = array_unique($data['category_ids']);
					$data['category_ids'] = implode(',', $data['category_ids']);
				}
				
				/* $data['general'] = json_encode($data['general']);
				$data['slider_options'] = json_encode($data['general']);
				$data['dispaly_settings'] = json_encode($data['general']); */
				$data['general'] = json_encode($data['general']);
				$data['slider_options'] = json_encode($data['slider_options']);
				$data['display_settings'] = json_encode($data['display_settings']); 
				$model->setData($data);
				$model->save();
				
				$this->messageManager->addSuccess(__('Product listing was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', ['id' => $model->getListingId(), '_current' => true]);
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
               // $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('finder_id')]);
            return;
		}
		$this->_redirect('*/*/');
	}
	
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlisting::productlisting_content');
    }
}