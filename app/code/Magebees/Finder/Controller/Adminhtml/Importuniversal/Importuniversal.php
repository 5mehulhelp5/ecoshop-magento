<?php
namespace Magebees\Finder\Controller\Adminhtml\Importuniversal;

use Magento\Framework\App\Filesystem\DirectoryList;

class Importuniversal extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $files = $this->getRequest()->getFiles();
        $finder_id     = $data['finder_id'];
        if (isset($files['filename']['name']) && $files['filename']['name'] != '') {
            try {
                $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'filename']);
                $allowed_ext_array = ['csv'];
                $uploader->setAllowedExtensions($allowed_ext_array);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::VAR_DIR);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('import/'));
                $path = $mediaDirectory->getAbsolutePath('import');
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('*/import/index', ['fid' => $finder_id]);
                return;
            }
            
            $handle = fopen($path.$result['file'], "r");
            $row=0;
            
            $model_finder = $this->_objectManager->create('Magebees\Finder\Model\Finder')->load($finder_id);
                
            /**************Delete Existing records if user selected "YES" *************************/
            if ($data['existing_data']) {
                $model_imported_products = $this->_objectManager->create('Magebees\Finder\Model\UniversalProduct')->getCollection()->addFieldToFilter('finder_id', $finder_id);
                $model_imported_products->walk('delete');
            }
            /***************************************************************/

            while (($skus = fgetcsv($handle, 1000, ",")) !== false) {
                foreach ($skus as $sku) {
                    $product_id= $this->_objectManager->create('Magento\Catalog\Model\Product')->getIdBySku($sku);
                    if ($product_id) {
                        $sku_exit =  $this->_objectManager->create('Magebees\Finder\Model\UniversalProduct')->getCollection()
                            ->addFieldToFilter('finder_id', $finder_id)
                            ->addFieldToFilter('sku', $sku)->getColumnValues('sku');

                        if (!empty($sku_exit)) {
                            $this->messageManager->addNotice($sku." already exist in universal products");
                            continue;
                        }

                        try {
                            $this->_objectManager->create('Magebees\Finder\Model\UniversalProduct')
                                ->setFinderId($finder_id)
                                ->setSku($sku)
                                ->setProductId($product_id)
                                ->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addException($e, __('Something went wrong while saving the product.'));
                        }
                    } else {
                        $this->messageManager->addError($sku." dose not exist");
                    }
                }
                $this->messageManager->addSuccess(__('Universal Products has been Successfully Imported.'));
            }
                    
            $this->_redirect('*/finder/edit', ['id' => $finder_id]);
            return;
        }
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
