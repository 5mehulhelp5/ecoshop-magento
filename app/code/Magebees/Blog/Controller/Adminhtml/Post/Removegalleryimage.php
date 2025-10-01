<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Json\Helper\Data as JsonHelper;
class Removegalleryimage extends \Magento\Backend\App\Action {
    
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    public $_storeManager;
    protected $_file;
	protected $resultJsonFactory;
    protected $jsonHelper;
	protected $_post;
	
	
   public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonHelper $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Driver\File $file,
	   \Magebees\Blog\Model\Post $post
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_file = $file;
	   $this->_post = $post;
    }

    
   public function execute(){
        
        $_postData = $this->getRequest()->getPost()->toarray();
       
        $message = "";
        $newFileName = "";
        $success = false;
        $newgallerylist = "";
        $mediaRootDir = $this->_mediaDirectory->getAbsolutePath();
        $_fileName = $mediaRootDir .'magebees_blog/gallery/'. $_postData['filename'];
        
            try{
				$imageListHtml = null;
				if(isset($_postData['postID']))
				{
				$postId = $_postData['postID'];
				$postDetails = $this->_post->load($postId);
				$current_media_gallery = $postDetails->getMediaGallery();
				
				$current_media_gallery_arr = explode(",",(string)$current_media_gallery);
				unset($current_media_gallery_arr[array_search($_postData['filename'], $current_media_gallery_arr)]);
				$newgallerylist = implode(",",(array)$current_media_gallery_arr);
				$postDetails->setMediaGallery($newgallerylist);
				$postDetails->save();				
				}
				
				/*
				$imageList = $_postData['imageList'];
				$newgallerylist = explode(",",$imageList);
				if(count($newgallerylist)>0)
				{ 
				unset($newgallerylist[array_search($_postData['filename'], $newgallerylist)]);
				}*/
				if ($this->_file->isExists($_fileName))  {
	                $this->_file->deleteFile($_fileName);
					$message = "File removed successfully.";
                	$success = true;
					}else{
    		        $message = "File not found.";
		            $success = true;
        		}
				$data = array('html' => $imageListHtml ,'remainingGalleryList' => $newgallerylist);
				
				
                
            } catch (Exception $ex) {
                $message = $e->getMessage();
                $success = false;
            }
        
        $resultJson = $this->resultJsonFactory->create();
	return $resultJson->setData([
                    'message' => $message,
                    'data' => '',
					'remainingGalleryList' => $newgallerylist,
                    'success' => $success
        ]);         
    }
}
