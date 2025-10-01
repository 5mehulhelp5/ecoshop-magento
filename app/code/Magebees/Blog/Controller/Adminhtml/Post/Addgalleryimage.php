<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Json\Helper\Data as JsonHelper;
class Addgalleryimage extends \Magento\Backend\App\Action {
    
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    public $_storeManager;
	protected $jsonHelper;
    protected $resultJsonFactory;
	protected $_post;
	

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonHelper $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magebees\Blog\Model\Post $post
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
		$this->_post = $post;
    }

    
    public function execute(){
        
		
		$files = $this->getRequest()->getFiles()->toArray();
		$_postData = $this->getRequest()->getPost()->toArray();
      
		$message = "";
        $newFileName = "";
        $error = false;
        $data = array();
        $ImageUploadedList = array();
        try{
			
			if(isset($files['attachment'])) {
				
				
                
				$files_count = count($files['attachment']);
               // echo is_array($files_arr['upload_file']);exit;
                                
				$target = $this->_mediaDirectory->getAbsolutePath('magebees_blog/gallery/'); 
				 $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				$imageListHtml = null;
				$uploadedFileListarr = array();
				$message = "File has been successfully uploaded";
				for($i=0;$i<$files_count;$i++){
                    $fileId = $i;
                    if(isset($files['attachment'][$fileId]['name']) && $files['attachment'][$fileId]['name'] != ''){
						
							$uploader = $this->_fileUploaderFactory->create(['fileId' => $files['attachment'][$fileId]]);
							$_fileType = $uploader->getFileExtension();
            		 		//$newFileName = uniqid().'.'.$_fileType;
							
							$newFileName = preg_replace('/\s+/', '_', $files['attachment'][$fileId]['name']);
						
						
							$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
							
							$uploader->setAllowRenameFiles(true);
							$uploader->setFilesDispersion(true);
							$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
								->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
							//$result = $uploader->save($mediaDirectory->getAbsolutePath('blog/post/gallery/'));
							
							$result = $uploader->save($target, $newFileName); //Use this if you want to change your file name
							$newFileName = $result['file'];
						$uploadedFileListarr[] = $newFileName;
							$image_Name = $result['name'];
						 	$_src = $_mediaUrl.'magebees_blog/gallery'.$result['file'];
							
						 $imageListHtml .= '<div class="image item base-image" data-role="image" id="'. uniqid().'">
                            <div class="product-image-wrapper">
                                <img class="product-image" data-role="image-element" src="'.$_src.'" alt="">
                                <div class="actions">
                                    <button type="button" class="action-remove" data-role="delete-button" data-image="'.$newFileName.'" title="Delete image"><span>Delete image</span></button>
                                </div>
                                <div class="image-fade"><span>Hidden</span></div>
                            </div>
                            <div class="item-description">
                                <div class="item-title" data-role="img-title"></div>
                                <div class="item-size">
                                    <a href="'.$_src.'" target="_blank"><span data-role="image-dimens">'.$image_Name.'</span></a>
                                </div>
                            </div>
                        </div>';
						
							
							unset($result['tmp_name']);
							unset($result['path']);
							
							
					}
				}
			
				
				
			}
			
			$uploadedFileList = implode(",",(array)$uploadedFileListarr);
			
			
			if(isset($_postData['post_id']))
			{
			$postId = $_postData['post_id'];
			$postDetails = $this->_post->load($postId);
			$current_media_gallery = $postDetails->getMediaGallery();
			
			$current_media_gallery_arr = explode(",",(string)$current_media_gallery);
				$newgallerylistarr = array_merge($current_media_gallery_arr,$uploadedFileListarr);
				$newgallerylist = implode(",",(array)$newgallerylistarr);
				$postDetails->setMediaGallery($newgallerylist);
				$postDetails->save();				
			}
				$data = array('html' => $imageListHtml ,'uploadedFiles' => $uploadedFileList);
			
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }
        
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
                    'message' => $message,
                    'data' => $data,
                    'error' => $error
        ]);
    }
}
