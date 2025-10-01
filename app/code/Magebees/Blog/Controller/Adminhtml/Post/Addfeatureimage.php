<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Json\Helper\Data as JsonHelper;
class Addfeatureimage extends \Magento\Backend\App\Action {
    
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
        
		 $_postData = $this->getRequest()->getPost()->toArray();
		
		
		$files = $this->getRequest()->getFiles()->toArray();
		$message = "";
        $newFileName = "";
        $error = false;
        $data = array();
        $ImageUploadedList = array();
        try{
			
			if(isset($files['featured'])) {
				
				
                
				$files_count = count($files['featured']);
               // echo is_array($files_arr['upload_file']);exit;
                                
				$target = $this->_mediaDirectory->getAbsolutePath('magebees_blog/'); 
				 $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				$imageListHtml = null;
				$uploadedFileList = array();
				$message = "File has been successfully uploaded";
					
				if(isset($files['featured']['name']) && $files['featured']['name'] != ''){
						
						$uploader = $this->_fileUploaderFactory->create(['fileId' => $files['featured']]);
							$_fileType = $uploader->getFileExtension();
            		 		$newFileName = preg_replace('/\s+/', '_', $files['featured']['name']);
							$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
							
							$uploader->setAllowRenameFiles(true);
							$uploader->setFilesDispersion(true);
							
							
							$result = $uploader->save($target, $newFileName); //Use this if you want to change your file name
					
					
							$newFileName = $result['file'];
							$image_Name = $result['name'];
						$uploadedFileList[] = $newFileName;
						
						 	$_src = $_mediaUrl.'magebees_blog'.$result['file'];
							
						 $imageListHtml = '<div class="image item base-image" data-role="image" id="'. uniqid().'">
                            <div class="product-image-wrapper">
                                <img class="product-image" data-role="image-element" src="'.$_src.'" alt="">
                                <div class="actions">
                                    <button type="button" class="action-featured-remove" data-role="delete-button" data-image="'.$newFileName.'" title="Delete image"><span>Delete image</span></button>
                                </div>
                                <div class="image-fade"><span>Hidden</span></div>
                            </div>
                            <div class="item-description">
                                <div class="item-title" data-role="img-title"></div>
                                <div class="item-size">
                                    <a href="'.$_mediaUrl.'magebees_blog'.$result['file'].'" target="_blank"><span data-role="image-dimens">'.$image_Name.'</span></a>
                                </div>
                            </div>
                        </div>';
						
							
							unset($result['tmp_name']);
							unset($result['path']);
							
							
					}
				
			
				if(isset($_postData['post_id']))
				{
				$postId = $_postData['post_id'];
				$postDetails = $this->_post->load($postId);
				$postDetails->setFeaturedImg($newFileName);
				$postDetails->save();
					
				}
				
			}
			
			$uploadedFileList = implode(",",(array)$uploadedFileList);
			
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
