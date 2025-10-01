<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
class MassDelete extends \Magento\Backend\App\Action
{
    protected $helper;
	protected $post;
     public function __construct(
        \Magento\Backend\App\Action\Context $context,
    	\Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    	$this->post = $post;
		$this->helper = $helper;

    }
 
    public function execute()
    {
       
        $postIds = $this->getRequest()->getParam('postIds');
        
        if (!is_array($postIds) || empty($postIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($postIds);
                foreach ($postIds as $postId) {
					$data = array();
                    $post= $this->post->load($postId);
					$data['identifier'] = $post->getIdentifier();
					$post->delete();
					$this->_eventManager->dispatch('magebees_blog_rewrite_url_delete', ['data' => $data]);
                }
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been deleted.', count($postIds))
                );
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
