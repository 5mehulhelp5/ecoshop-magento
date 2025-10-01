<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;

class Delete extends \Magento\Backend\App\Action
{
   
	protected $post;
	protected $helper;
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
        $post_id = $this->getRequest()->getParam('post_id');
        try {
				$data = array();
                $post = $this->post->load($post_id);
				$data['identifier'] = $post->getIdentifier();
				$post->delete();
				$this->_eventManager->dispatch('magebees_blog_rewrite_url_delete', ['data' => $data]);
                $this->messageManager->addSuccess(
                    __('Post was deleted successfully!')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
