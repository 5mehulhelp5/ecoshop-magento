<?php
namespace Magebees\Pagebanner\Controller\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $_jsHelper;
    protected $PagebannerFactory;
    protected $_filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magebees\Pagebanner\Model\PagebannerFactory $PagebannerFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->_jsHelper = $jsHelper;
        $this->PagebannerFactory = $PagebannerFactory;
        $this->_filesystem = $filesystem;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost()->toArray();
        $files =  $this->getRequest()->getFiles();

        if ($data) {
            $id = $this->getRequest()->getParam('banner_id');

            $model = $this->_objectManager->create('Magebees\Pagebanner\Model\Pagebanner');
            $files =  $this->getRequest()->getFiles();

            if (isset($data['stores']) && is_array($data['stores'])) {
                $data['stores'] = array_unique($data['stores']);
                $data['stores'] = implode(',', $data['stores']);
            }

            $pageBannerCollection = $this->PagebannerFactory->create()->getCollection();
            $stores = $data['stores'];
            $status = $data['status'];
            $page_type = $data['page_type_options'];

            $pageBannerCollection->addFieldToFilter('stores', array('eq' => $stores));
            $pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
            if ($id) {
                $pageBannerCollection->addFieldToFilter('banner_id', array('neq' => $id));
            }

            if ($page_type == 'cmspage') {
                $cms_page = $data['cms_page'];
                $pageBannerCollection->addFieldToFilter('cms_page', array('eq' => $cms_page));
            } else if ($page_type == 'catalogcategory') {
                $catalog_category = $data['catalog_category'];
                $pageBannerCollection->addFieldToFilter('catalog_category', array('eq' => $catalog_category));
            } else if ($page_type == 'blogcategory') {
                $blog_category = $data['blog_category'];
                $pageBannerCollection->addFieldToFilter('blog_category', array('eq' => $blog_category));
            } else if ($page_type == 'specifiedpage') {
                $layout_handle = $data['layout_handle'];
                $pageBannerCollection->addFieldToFilter('layout_handle', array('eq' => $layout_handle));
            }

            if ($pageBannerCollection->getSize() > 0) {
                if ($id) {
                    $model->load($id);
                }
                $this->messageManager->addError(__('Page Banner already exists for this page'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getBannerId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/new');
                return;
            }

            if ($id) {
                $model->load($id);
            }

            // Handle the image file upload
            if (isset($files['banner_image']['name']) && $files['banner_image']['name'] != '') {
                try {
                    // Handle the image upload
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'banner_image'));
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'webp'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('pagebanner'));

                    // Remove temporary file info
                    unset($result['tmp_name']);
                    unset($result['path']);

                    // Store the image file path
                    $data['banner_image'] = $result['file'];

                    // Get image dimensions
                    $imagePath = $mediaDirectory->getAbsolutePath('pagebanner' . DIRECTORY_SEPARATOR . $result['file']);
                    list($width, $height) = getimagesize($imagePath);

                    // Save the dimensions in the data
                    $data['banner_image_width'] = $width;
                    $data['banner_image_height'] = $height;
					

                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Please Select a Valid Image File'));
                    $this->_redirect('*/*/edit', ['id' => $model->getBannerId(), '_current' => true]);
                    return;
                }
            } else {
                if (isset($data['banner_image']['delete']) && $data['banner_image']['delete'] == 1) {
                    $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                    if (isset($data['banner_image']['value'])) {
                        $bannerpath = '/'.$data['banner_image']['value'];
                        $bannerdir = $mediaDir->getAbsolutePath($bannerpath);
                        if (file_exists($bannerdir)) {
                            unlink($bannerdir);
                        }
                    }
                    $data['banner_image'] = '';
                } else {
                    unset($data['banner_image']);
                }
            }

            try {
                // Save the model data
                $model->setData($data);
                $model->save();

                $this->messageManager->addSuccess(__('Page Banner was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getBannerId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('banner_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Pagebanner::pagebanner_content');
    }
}
