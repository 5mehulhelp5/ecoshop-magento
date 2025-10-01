<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
class Massstatus extends \Magento\Backend\App\Action
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
       
		$delivered_status = (int) $this->getRequest()->getPost('status');
        $postIds = $this->getRequest()->getParam('postIds');
        
        if (!is_array($postIds) || empty($postIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($postIds);
                foreach ($postIds as $postId) {
                    $post= $this->post->load($postId)->setIsActive($delivered_status)->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) Status have been Updated.', count($postIds))
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
