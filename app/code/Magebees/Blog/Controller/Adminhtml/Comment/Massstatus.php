<?php
namespace Magebees\Blog\Controller\Adminhtml\Comment;
class Massstatus extends \Magento\Backend\App\Action
{
    protected $comment;
	protected $helper;
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
    	\Magebees\Blog\Model\Comment $comment,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    	$this->comment = $comment;
		$this->helper = $helper;

    }
 
    public function execute()
    {
       
		$delivered_status = (int) $this->getRequest()->getPost('status');
        $commentIds = $this->getRequest()->getParam('commentIds');
        
        if (!is_array($commentIds) || empty($commentIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($commentIds);
                foreach ($commentIds as $commentId) {
                    $tag= $this->comment->load($commentId)->setStatus($delivered_status)->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) Status have been Updated.', count($commentIds))
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
