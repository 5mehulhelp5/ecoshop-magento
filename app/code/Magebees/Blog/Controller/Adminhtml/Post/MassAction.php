<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
class MassAction extends \Magento\Backend\App\Action
{
	
	protected $post;
     public function __construct(
        \Magento\Backend\App\Action\Context $context,
    	\Magebees\Blog\Model\Post $post
	) {
    
        parent::__construct($context);
    	$this->post = $post;
	}
 
    public function execute()
    {
       
		
		$data = $this->getRequest()->getPost();
		$postIds = $this->getRequest()->getParam('postIds');
        if (!is_array($postIds) || empty($postIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($postIds);
                foreach ($postIds as $postId) {
					$post= $this->post->load($postId);
					if(isset($data['draft']))
					{
					$post_draft = (int)$data['draft'];
					$post->setIsActive($post_draft)->save();
					}
					if(isset($data['is_featured']))
					{
					$is_featured = (int)$data['is_featured'];
					$post->setIsFeatured($is_featured)->save();
					}
					if(isset($data['include_in_recent']))
					{ 
					$include_in_recent = (int)$data['include_in_recent'];
					$post->setIsRecentPostsSkip($include_in_recent)->save();
					}
					
                    //$post= $this->post->load($postId)->setIsActive($post_draft)->save();
				}
				if(isset($data['draft']))
				{
					$this->messageManager->addSuccess(__('A total of   '.$count .'  record(s) Set As Draft have been Updated.', count($postIds)));
				}
				if(isset($data['is_featured']))
				{
					$this->messageManager->addSuccess(__('A total of   '.$count .'  record(s) Set As Is Featured have been Updated.', count($postIds)));
				}
				if(isset($data['include_in_recent']))
				{
					$this->messageManager->addSuccess(__('A total of   '.$count .'  record(s) Set As Recent have been Updated.', count($postIds)));
				}
                
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}

