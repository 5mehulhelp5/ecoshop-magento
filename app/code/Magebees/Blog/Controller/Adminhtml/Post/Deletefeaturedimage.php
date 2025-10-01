<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Json\Helper\Data as JsonHelper;
class Deletefeaturedimage extends \Magento\Backend\App\Action {
    
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    public $_storeManager;
    protected $jsonHelper;
	protected $resultJsonFactory;
	protected $_post;
	protected $_file;
	

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
	   $this->_post = $post;
        $this->_file = $file;
    }

    
   public function execute(){
        
        $_postData = $this->getRequest()->getPost();
        
        $message = "";
        $newFileName = "";
        $success = false;
        $mediaRootDir = $this->_mediaDirectory->getAbsolutePath();
        $_fileName = $mediaRootDir .'magebees_blog'. $_postData['filename'];
       
	   
	   if ($this->_file->isExists($_fileName))  {
            try{
                $this->_file->deleteFile($_fileName);
				$postId = $_postData['postID'];
	   $postDetails = $this->_post->load($postId);
				$postDetails->setFeaturedImg(null);
				$postDetails->save();		
                $message = "File removed successfully.";
                $success = true;
            } catch (Exception $ex) {
                $message = $e->getMessage();
                $success = false;
            }
        }else{
            $message = "File not found.";
            $success = true;
        }
        
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
                    'message' => $message,
                    'data' => '',
                    'success' => $success
        ]);         
    }
}
