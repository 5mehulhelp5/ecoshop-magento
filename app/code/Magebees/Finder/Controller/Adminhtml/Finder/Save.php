<?php
namespace Magebees\Finder\Controller\Adminhtml\Finder;

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
        $id = $this->getRequest()->getParam('finder_id');
        if ($data) {
            if (isset($data['links'])) {
                $data['product_id'] = $this->_jsHelper->decodeGridSerializedInput($data['links']['universal']);
            } else {
                $data['product_id'] = $this->_objectManager->create('Magebees\Finder\Model\UniversalProduct')->getCollection()
                ->addFieldToFilter('finder_id', ['eq' => $id])->getColumnValues('product_id');
            }
        
            $model = $this->_objectManager->create('Magebees\Finder\Model\Finder');
            $id = $this->getRequest()->getParam('finder_id');
            if ($id) {
                $model->load($id);
            }
            try {
                if (isset($data['category_ids']) && is_array($data['category_ids'])) {
                    $data['category_ids'] = array_unique($data['category_ids']);
                    $data['category_ids'] = implode(',', $data['category_ids']);
                }
                
                $model->setData($data);
                $model->save();
                
                if (isset($data['name']) && is_array($data['name'])) {
                    $dropdown_data = [];
                   
                    for ($k=0; $k<count($data['name']); $k++) {
						 $model_dropdwn = $this->_objectManager->create('Magebees\Finder\Model\Dropdowns');
                        $dropdown_data = array_map('trim', ['finder_id'=> $data['finder_id'],'name'=> $data['name'][$k],'sort'=> $data['sort'][$k]]);
                        if ($data['ids'][$k]) {
                            $model_dropdwn->setData($dropdown_data)->setId($data['ids'][$k]);
                        } else {
                            $model_dropdwn->setData($dropdown_data);
                        }
                        $model_dropdwn->save();
                    }
                }
                
                $this->messageManager->addSuccess(__('Finder was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getFinderId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the finder.'));
                //$this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('finder_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder');
    }
}
