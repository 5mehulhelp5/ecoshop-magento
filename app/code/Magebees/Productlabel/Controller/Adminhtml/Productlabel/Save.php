<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    
    protected $_labelHelper;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Productlabel\Helper\Data $labelHelper
    ) {
        parent::__construct($context);
        $this->_labelHelper = $labelHelper;
    }
    
    public function execute()
    {
        $data = $this->getRequest()->getPost()->toArray();
        $id = $this->getRequest()->getParam('label_id');
        
        if ($data) {
            $model = $this->_objectManager->create('Magebees\Productlabel\Model\Productlabel');
           
            if ($id) {
                $model->load($id);
            }
                    
            try {
                $data = $this->_filterPicture($data);//for rename and move to specific directory
                if (array_key_exists('fail', $data)) {
                    $this->messageManager->addError(__($data['fail']));
                    $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('label_id')]);
                    return;
                }
                //validate from and to date
                if ($data['date_enabled']) {
                    $validateResult = $model->validateData($data);
                    if ($validateResult == false) {
                        $this->messageManager->addError(__('To Date must be greater than From Date.'));
                        $this->_redirect('*/*/edit', ['id' => $model->getLabelId(), '_current' => true]);
                        return;
                    }
                }
                if (in_array("0", $data['stores'])) {
                    $allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
                    $storeids = [];
                    $storeids[0] = 0;
                    foreach ($allStores as $_eachStoreId => $val) {
                        $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId)->getId();
                        $storeids[] = $_storeId;
                    }
                    $data['stores'] = $storeids;
                }
				
				if (isset($data['rule']) && isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];

                    unset($data['rule']);

                    $rule = $this->_objectManager->create('Magebees\Productlabel\Model\Rule');
                    $rule->loadPost($data);

					
					$serialize = $this->_objectManager->create('Magento\Framework\Serialize\Serializer\Json');
					$data['cond_serialize'] = $serialize->serialize($rule->getConditions()->asArray());
					
                    unset($data['conditions']);
                }
                
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccess(__('Label was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getLabelId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the label.'));
                //$this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('label_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    protected function _filterPicture($data)
    {
        $files = $this->getRequest()->getFiles();
        $fieldName = ['cat_image', 'prod_image'];
        foreach ($fieldName as $field) {
            $width_index = $field."_width";
            $height_index = $field."_height";
            $data[$width_index] = !empty($data[$width_index])?$data[$width_index]:80;
            $data[$height_index] = !empty($data[$height_index])?$data[$height_index]:80;
            if (isset($files[$field]['name']) && $files[$field]['name'] != '') {
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => $field]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($field));
                    chmod($result['path'], 0775);
                    unset($result['tmp_name']);
                    unset($result['path']);
                    $data[$field] = $result['file'];
                    
                    $this->_labelHelper->resizeImg($field, $data[$field], $data[$width_index], $data[$height_index]);//resize image
                } catch (\Exception $e) {
                    $data['fail']= $e->getMessage();
                }
            } else {
                if (isset($data[$field]['delete']) && $data[$field]['delete'] == 1) {
                    $data[$field] = '';
                } else {
                    if (isset($data[$field])) {
                        $this->_labelHelper->resizeImg($field, $data[$field]['value'], $data[$width_index], $data[$height_index]);//resize image
                        unset($data[$field]);
                    }
                }
            }
        }
        return $data;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Productlabel::label');
    }
}
