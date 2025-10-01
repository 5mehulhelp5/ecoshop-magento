<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Brands;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $helper = $this->_objectManager->create('Magebees\Layerednavigation\Helper\Data');
        $data = $this->getRequest()->getPost()->toarray();
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
        if ($data) {
            $model = $this->_objectManager->create('Magebees\Layerednavigation\Model\Brands');
            //$file = $this->getFileData();
            
            $filedata=$this->getRequest()->getFiles()->toArray();
            if (isset($filedata['filename']['name']) && $filedata['filename']['name'] != '') {
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'filename']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('layernav_brand'));
                    unset($result['tmp_name']);
                    unset($result['path']);
                    $data['filename'] = $result['file'];
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Please Select Valid Image File'));
                    $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('brand_id'), '_current' => true]);
                    return;
                }
            } else {
                if (isset($data['filename']['delete']) && $data['filename']['delete'] == 1) {
                    $img_path=$mediaDirectory->getAbsolutePath('layernav_brand');
                    $img=explode("layernav_brand", (string)$data['filename']['value']);
                    $img_name=$img[1];
                    $unlink_img=$img_path.$img_name;
                    unlink($unlink_img);
                    $data['filename'] = '';
                } else {
                    unset($data['filename']);
                }
            }
            $id = $this->getRequest()->getParam('brand_id');
        
            if ($id) {
                $model->load($id);
            }
        
            $model->setData($data);

            try {
                $model->save();
        
                $this->messageManager->addSuccess(__('Brand was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('brand_id'), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the slide.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('brand_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::brand');
    }
}
