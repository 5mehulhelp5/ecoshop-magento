<?php
namespace Magebees\Finder\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class UploadFile extends \Magento\Backend\App\Action
{
   
 
    public function execute()
    {
        $data = $this->getRequest()->getPost();
		
        $files = $this->getRequest()->getFiles();
		
        $finder_id = $data['finder_id'];
        if ($data) {
            if (isset($files['filename']['name']) && $files['filename']['name'] != '') {
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'filename']);
                    $allowed_ext_array = ['csv'];
                    $uploader->setAllowedExtensions($allowed_ext_array);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('import/ymm'));
                    $path = $mediaDirectory->getAbsolutePath('import/ymm');
                } catch (\Exception $e) {
                    $this->messageManager->addError(__($e->getMessage()));
                    $this->_redirect('*/import/index', ['fid' => $finder_id]);
                    return;
                }
            }
            $dropdownsCol = $this->_objectManager->create('Magebees\Finder\Model\Dropdowns')->getCollection();
            $dropdownsCol->addFieldToFilter('finder_id', $finder_id);
            $dropdown_ids = $dropdownsCol->load()->getColumnValues('dropdown_id');
			
            //check csv columns
            $id = fopen($path."/".$result['file'], "r");
            $file = fgetcsv($id);
            $dataincsv = count($file) - 1;
            if ($dataincsv != count($dropdown_ids)) {
                $this->messageManager->addError(__('CSV columns mismatch. Please check CSV file and upload again.'));
                $this->_redirect('*/import/index', ['fid' => $finder_id]);
                return;
            }
 			$this->messageManager->addSuccess(__('File Uploaded Successfully'));
            $this->_redirect('*/import/index', ['fid' => $finder_id,'active_tab'=>'import_section']);
             return;
        }
    }
	
	    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
