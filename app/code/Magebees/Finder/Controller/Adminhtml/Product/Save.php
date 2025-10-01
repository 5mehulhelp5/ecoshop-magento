<?php
namespace Magebees\Finder\Controller\Adminhtml\Product;

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
        if ($data = $this->getRequest()->getPost()->toArray()) {
            $map_id = $this->getRequest()->getParam('id');
            $finder_id = $this->getRequest()->getParam('finder_id');
            $dropdown_ids = [];
            if (isset($data['links'])) {
                $product_sku = [];
                $data['product_id'] = $this->_jsHelper->decodeGridSerializedInput($data['links']['finder']);
                foreach ($data['product_id'] as $pro_id) {
                    $product_sku[] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($pro_id)->getSku();
                }
                
                $data['sku'] = implode("|", $product_sku);
                $data['product_id'] = implode("|", $data['product_id']);
            } else {
                $mapvalueModel = $this->_objectManager->create('Magebees\Finder\Model\Mapvalue')->load($map_id);
                $data['product_id'] = $mapvalueModel->getProductId();
                $data['sku'] = $mapvalueModel->getSku();
            }
        
            
            if ($map_id) {
                foreach ($data as $key => $val) {
                    if (substr($key, 0, 6) == 'label_') {
                        $valueId = (int)(substr($key, 6));
                        $value = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue')->load($valueId);
                        $dropdownId =  $value->getDropdownId();
                        $dropdown_ids[] = $value->getDropdownId();
                        unset($data[$key]);
                        $data['label_'.$dropdownId] = $val;
                    }
                }
                try {
                    $mapModel = $this->_objectManager->create('Magebees\Finder\Model\Mapvalue')->load($map_id);
                    $ymm_value_id = $mapModel->getYmmValueId();
                    $mapModel->delete();
                    $finderModel = $this->_objectManager->create('Magebees\Finder\Model\Finder');
                    $currentId = $ymm_value_id;
                    $ymmvalueModel = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue')->load($ymm_value_id);
                    $dropdownId =  $ymmvalueModel->getDropdownId();
                    while ($ymm_value_id && $finderModel->isDeleteYmmData($ymm_value_id)) {
                        $ymmvalueModel = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue')->load($ymm_value_id);
                        $ymm_value_id = $ymmvalueModel->getParentId();
                        $dropdownId =  $ymmvalueModel->getDropdownId();
                        $ymmvalueModel->delete();
                    }
                } catch (\Exception $e) {
                //$this->messageManager->addException($e, __('Something went wrong while saving the data.'));
                    $this->messageManager->addError($e->getMessage());
                }
            } else {
                $dropdownsCol = $this->_objectManager->create('Magebees\Finder\Model\Dropdowns')->getCollection();
                $dropdownsCol->addFieldToFilter('finder_id', $finder_id);
                $dropdown_ids = $dropdownsCol->load()->getColumnValues('dropdown_id');
            }
            
            sort($dropdown_ids);
            $value_data = [];
            $index = '';
                
            $parent_id = 0;
            foreach ($dropdown_ids as $dropdown_id) {
                $index = 'label_'.$dropdown_id;
                $valueModel = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue');
                $exits = $valueModel->getCollection()
                    ->addFieldToFilter('value', trim($data[$index]))
                    ->addFieldToFilter('parent_id', $parent_id)
                    ->addFieldToFilter('dropdown_id', $dropdown_id);
                $isExists = $exits->getData();
                if (empty($isExists)) {
                    $value_data['dropdown_id'] = $dropdown_id;
                    $value_data['parent_id'] = $parent_id;
                    $value_data['value'] = trim($data[$index]);
                    $valueModel->setData($value_data)->save();
                    $parent_id = $valueModel->getId();
                } else {
                    $parent_id = $exits->getFirstItem()->getYmmValueId();
                }
            }
            $ymm_value_id = $parent_id;
            $mapModel = $this->_objectManager->create('Magebees\Finder\Model\Mapvalue')->load($ymm_value_id, 'ymm_value_id');
            $map_value = [];
            $map_value['ymm_value_id'] = $ymm_value_id;
            $map_value['sku'] = $data['sku'];
            $map_value['product_id'] = $data['product_id'];
            
            if ($mapModel->getId()) { // if exist then concate with old
                $map_value['map_value_id'] = $mapModel->getId();
                $map_value['sku'] = $mapModel->getSku()."|".$map_value['sku'];
                $map_value['product_id'] = $mapModel->getProductId()."|".$map_value['product_id'];
            }
            
            try {
                $mapModel->setData($map_value);
                $mapModel->save();
                                            
                $this->messageManager->addSuccess(__('Product details was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/finder/edit', ['id' => $data['finder_id'], '_current' => true]);
                    return;
                }
                $this->_redirect('*/finder/edit', ['id' => $data['finder_id'], '_current' => true]);
                return;
            } catch (\Exception $e) {
                //$this->messageManager->addException($e, __('Something went wrong while saving the data.'));
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/finder/edit', ['id' => $finder_id, '_current' => true]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
