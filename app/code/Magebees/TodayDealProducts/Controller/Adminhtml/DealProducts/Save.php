<?php
namespace Magebees\TodayDealProducts\Controller\Adminhtml\DealProducts;

class Save extends \Magento\Backend\App\Action
{
    
    public function execute()
    {
        $data = $this->getRequest()->getPost()->toArray();
        
        $id = $this->getRequest()->getParam('today_deal_id');
        $jsHelper = $this->_objectManager->create('Magento\Backend\Helper\Js');
        if ($data) {
            $model = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Deal');
            
            if ($id) {
                $model->load($id);
            }
                    
            try {
                //set layout options
                $data['layoutoptions'] = json_encode($data['layout']);
                
                
                //validate from and to date
                $validateResult = $model->validateDate($data);
                if ($validateResult == false) {
                    $this->messageManager->addError(__('To Date must be greater than From Date.'));
                    $this->_redirect('*/*/edit', ['id' => $model->getTodayDealId(), '_current' => true]);
                    return;
                }
                
                
                if (in_array("0", $data['stores'])) {
                    $allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
                    $storeids = [];
                    //$storeids[0] = 0;
                    foreach ($allStores as $_eachStoreId => $val) {
                        $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId)->getId();
                        $storeids[] = $_storeId;
                    }
                    $data['stores'] = $storeids;
                }
                
                if (isset($data['rule']) && isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];

                    unset($data['rule']);

                    $rule = $this->_objectManager->create('Magebees\TodayDealProducts\Model\Rule');
                    $rule->loadPost($data);

					$serialize = $this->_objectManager->create('Magento\Framework\Serialize\Serializer\Json');
					$data['cond_serialize'] = $serialize->serialize($rule->getConditions()->asArray());
					
                    unset($data['conditions']);
                }
                
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccess(__('Deal was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getTodayDealId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                //$this->messageManager->addException($e, __('Something went wrong while saving the Deal.'));
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('today_deal_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function getProdcutIdsArr($element)
    {
        return $element['product_id'];
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_TodayDealProducts::todatydealpro_content');
    }
}
