<?php
namespace Magebees\Blog\Controller\Adminhtml\Comment;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{
	protected $_collection;
	protected $_scopeConfig;
	protected $comment;
	protected $_timezoneInterface;	
	protected $authSession;
	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Comment $comment,	
	\Magento\Framework\Module\Dir\Reader $reader,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	\Magento\Backend\Model\Auth\Session $authSession,
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_scopeConfig = $_scopeConfig;
		$this->comment = $comment;
		$this->_timezoneInterface = $timezoneInterface;
		$this->authSession = $authSession;
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
       	$data=$this->getRequest()->getPost()->toarray();
		if($data)
		{
			
			$id = $this->getRequest()->getParam('comment_id');
			$parent_comment_id = $this->getRequest()->getParam('parent_comment_id');
		    if(($id) && (!$parent_comment_id))
			{
                $this->comment->load($id);
				 if ($id != $this->comment->getCommentId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }
			try {
					if(($id) && (!$parent_comment_id))	
					{
						$this->comment->setData($data);		
						if ($this->comment->getCreatedTime() == NULL || $this->comment->getUpdateTime() == NULL) 
						{
							$this->comment->setCreatedTime($currentDate)
									->setUpdateTime($currentDate);
						} 
						else 
						{
							$this->comment->setUpdateTime($currentDate);
						}	
					$this->comment->save();
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					$this->_session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('comment_id' => $this->comment->getCommentId(), '_current' => true));
						return;
					}
					$this->_redirect('*/*/');
					return;	
					}else if((!$id) && ($parent_comment_id))	
					{
						$replydata=$this->getRequest()->getPost()->toarray();
						$parent_comment_id = $this->getRequest()->getParam('parent_comment_id');
						$comment = $this->comment->load($parent_comment_id);
						$post_id = $comment->getPostId();
						$current_user = $this->authSession->getUser();
						
						$admin_id = $current_user->getId();
						$replydata['parent_id'] = $parent_comment_id;
						$replydata['post_id'] = $post_id;
						$replydata['admin_id'] = $admin_id;
						$replydata['author_type'] = 'admin';
						$replydata['author_nickname'] = $current_user->getUsername();
						$replydata['author_email'] = $current_user->getEmail();
						
						
						$this->comment->setData($replydata);	
						
						if ($this->comment->getCreatedTime() == NULL || $this->comment->getUpdateTime() == NULL) 
						{
							$this->comment->setCreatedTime($currentDate)
									->setUpdateTime($currentDate);
						} 
						else 
						{
							$this->comment->setUpdateTime($currentDate);
						}
						$this->comment->save();
						$this->messageManager->addSuccess(__('The Record has been saved.'));
						$this->_session->setFormData(false);
							if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('comment_id' => $this->comment->getCommentId(), '_current' => true));
						return;
						}
						$this->_redirect('*/*/');
						return;	
					}
				}
				catch (\Magento\Framework\Model\Exception $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	}
				catch (\RuntimeException $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	} 
				catch (\Exception $e)
				{
					$this->messageManager->addError($e->getMessage());
                	
            	}
				$this->_getSession()->setFormData($data);
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($this->_redirect->getRefererUrl());
				return $resultRedirect;
				
		}
		$this->_redirect('*/*/');
    }
    
	protected function _isAllowed()
    {
		return true;
        
    }
	
}
