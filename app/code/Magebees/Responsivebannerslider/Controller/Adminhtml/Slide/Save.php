<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slide;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
	public function execute()
    {
		$helper = $this->_objectManager->create('Magebees\Responsivebannerslider\Helper\Data');
        $data = $this->getRequest()->getPost()->toarray();
		
        if ($data) {
			
			$files =  $this->getRequest()->getFiles();
            $model = $this->_objectManager->create('Magebees\Responsivebannerslider\Model\Slide');
					
            if(isset($files['filename']['name']) && $files['filename']['name'] != '') {
			
				try {
								
					$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'filename'));
					$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'webp'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
				
					$result = $uploader->save($mediaDirectory->getAbsolutePath('responsivebannerslider'));
					
					$imagePath = $result['path'].$result['file'];
					unset($result['tmp_name']);
					unset($result['path']);
					$data['filename'] = $result['file'];
					//$helper->resizeImg($data['filename']);
					
					$imagesrc = $helper->getBannerImage($data['filename']);
					//list($width, $height) = getimagesize($imagesrc);
					list($width, $height) = getimagesize($imagePath);
					$data['image_height'] = $height;
					$data['image_width'] = $width;
					
					
					
					
					
					
					
				}catch (\Exception $e) {
					$this->messageManager->addException($e, __($e->getMessage()));
					$this->messageManager->addException($e, __('Please Select Valid Image File'));
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('slide_id'), '_current' => true));
					return;
				} 
			}
			else{
				if (isset($data['filename']['delete']) && $data['filename']['delete'] == 1){
					$data['filename'] = '';
				}else {
					unset($data['filename']);
				}		
				
			}
			
			if(isset($files['filename_mobile']['name']) && $files['filename_mobile']['name'] != '') {
			
				try {
								
					$uploaders = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'filename_mobile'));
					$uploaders->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'webp'));
					$uploaders->setAllowRenameFiles(true);
					$uploaders->setFilesDispersion(true);
					$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
					$result = $uploaders->save($mediaDirectory->getAbsolutePath('responsivebannerslider'));
					$imagePath = $result['path'].$result['file'];
					
					unset($result['tmp_name']);
					unset($result['path']);
					$data['filename_mobile'] = $result['file'];
					
					$imagesrc = $helper->getBannerImage($data['filename_mobile']);
					//list($width, $height) = getimagesize($imagesrc);
					list($width, $height) = getimagesize($imagePath);
					
					$data['mobile_image_height'] = $height;
					$data['mobile_image_width'] = $width;
					
				//	$helper->resizeImg($data['filename_mobile']);
					
				}catch (\Exception $e) {
					$this->messageManager->addException($e, __($e->getMessage()));
					$this->messageManager->addException($e, __('Please Select Valid Image File'));
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('slide_id'), '_current' => true));
					return;
				} 
			}
			else{
				if (isset($data['filename_mobile']['delete']) && $data['filename_mobile']['delete'] == 1){
					$data['filename_mobile'] = '';
				}else {
					unset($data['filename_mobile']);
				}		
				
			}
			
			$id = $this->getRequest()->getParam('slide_id');
		    if ($id) {
                $model->load($id);
			}
			if(isset($data['hosted_thumb'])&&(!empty($data['hosted_thumb'])))
			{
				$hosted_thumb = $data['hosted_thumb'];
				list($width, $height) = getimagesize($hosted_thumb);
				
				$data['image_height'] = $height;
				$data['image_width'] = $width;
			}
			
			if(isset($data['hosted_url'])&&(!empty($data['hosted_url'])))
			{
				$hosted_url = $data['hosted_url'];
				list($width, $height) = getimagesize($hosted_url);
				$data['mobile_image_height'] = $height;
				$data['mobile_image_width'] = $width;
			}
			
			
		
			
			$model->setData($data);

            try {
				
				$group_label ='';
				for($i=0;$i<count($data['group_names']);$i++) {
					if($i < count($data['group_names'])-1){
						$group_label .= $data['group_names'][$i].",";
					}else{
						$group_label .= $data['group_names'][$i];
					}
				}
				
				if($data['date_enabled'] == 1) {
					$validateResult = $model->validateData($data);
					if ($validateResult == false) {
						$this->messageManager->addError(__('To Date must be greater than From Date.'));
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('slide_id'), '_current' => true));
						return;
					} 	
				}
				
				$model->setData("group_names",$group_label);
			    $model->save();
	
                $this->messageManager->addSuccess(__('Slide was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getSlideId(), '_current' => true));
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
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('slide_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
