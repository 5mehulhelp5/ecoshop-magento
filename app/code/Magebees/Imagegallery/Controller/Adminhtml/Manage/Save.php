<?php
namespace Magebees\Imagegallery\Controller\Adminhtml\Manage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
class Save extends \Magento\Backend\App\Action
{
	
    protected $helper;
	protected $ImagegalleryFactory;
	protected $_filesystem;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Imagegallery\Helper\Data $helper,
		\Magebees\Imagegallery\Model\ImagegalleryFactory $ImagegalleryFactory,
		Filesystem $filesystem
	) {
        parent::__construct($context);
        $this->helper = $helper;
		$this->ImagegalleryFactory = $ImagegalleryFactory;
		$this->_filesystem = $filesystem;
	}
	
	public function execute()
    {
		
		$data = $this->getRequest()->getPost()->toArray();
		$files =  $this->getRequest()->getFiles();
		
		$allowed_file_type = $this->helper->allowedFiletype();
		
		if($data){
			$id = $this->getRequest()->getParam('image_id');
			
			
			$model = $this->_objectManager->create('Magebees\Imagegallery\Model\Imagegallery');
			$files =  $this->getRequest()->getFiles();
			
			
			if (isset($data['stores']) && is_array($data['stores'])) {
					$data['stores'] = array_unique($data['stores']);
					$data['stores'] = implode(',', $data['stores']);
			}
			
			
			
					
			
			
			if ($id) {
                $model->load($id);
            }
			
			if(isset($files['image']['name']) && $files['image']['name'] != '') {
			
				try {
								
					$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'image'));
					if(count($allowed_file_type)>0){
					//$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
					$uploader->setAllowedExtensions($allowed_file_type);					
					}
					
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
					$result = $uploader->save($mediaDirectory->getAbsolutePath('imagegallery'));
					unset($result['tmp_name']);
					unset($result['path']);
					$data['image'] = $result['file'];
					
				}catch (\Exception $e) {
					$this->messageManager->addException($e, __('Please Select Valid Image File'));
					$this->messageManager->addException($e, __($e->getMessage()));
					$this->_redirect('*/*/edit', ['id' => $model->getImageId(), '_current' => true]);
					return;
				} 
			}
			else{
				if (isset($data['image']['delete']) && $data['image']['delete'] == 1){
					$mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
					if(isset($data['image']['value'])){
					$bannerpath = '/'.$data['image']['value'];
					$bannerdir = $mediaDir->getAbsolutePath($bannerpath);
					if (file_exists($bannerdir)) {
						unlink($bannerdir);
					}	
					}
					
					$data['image'] = '';
				}else {
					unset($data['image']);
				}		
				
			}
			
			try {
				
				
				
				$model->setData($data);
				$model->save();
				
				$this->messageManager->addSuccess(__('Image Gallery was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', ['id' => $model->getImageId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('image_id')]);
            return;
		}
		$this->_redirect('*/*/');
	}
	
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Imagegallery::imagegallery_content');
    }
}