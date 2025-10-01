<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Manage;

class Save extends \Magento\Backend\App\Action
{
    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
    }
    public function execute()
    {
        $data=$this->getRequest()->getPost()->toArray();
        $model = $this->_objectManager->create('Magebees\Layerednavigation\Model\Layerattribute');
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            if ($data['attribute_id']) {
                $resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                    $connection=$resources->getConnection();
                    $updateData = [
                    'show_in_block'=>$data['show_in_block'],
                    'always_expand'=>$data['always_expand'],
                    'sort_option'=>$data['sort_option'],
                    'tooltip'=>$data['tooltip'],
                    'robots_nofollow'=>$data['robots_nofollow'],
                    'robots_noindex'=>$data['robots_noindex'],
                    'rel_nofollow'=>$data['rel_nofollow'],
                    'include_cat'=>$data['include_cat'],
                    'exclude_cat'=>$data['exclude_cat'],
                    ];
                    if (isset($data['unfold_option'])) {
                        $updateData['unfold_option']=$data['unfold_option'];
                    }
                    if (isset($data['show_searchbox'])) {
                        $updateData['show_searchbox']=$data['show_searchbox'];
                    }
                    if (isset($data['show_product_count'])) {
                        $updateData['show_product_count']=$data['show_product_count'];
                    }
                    if (isset($data['display_mode'])) {
                        $updateData['display_mode']=$data['display_mode'];
                    }
                    if (isset($data['and_logic'])) {
                        $updateData['and_logic']=$data['and_logic'];
                    }
                    $whereCondition = ['attribute_id=?' =>$data['attribute_id']];
                    $table=$resources->getTableName('magebees_layernav_attribute');
                    $connection->update($table, $updateData, $whereCondition);
            }
            
            try {
                $this->messageManager->addSuccess(__('The Record has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Record.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
