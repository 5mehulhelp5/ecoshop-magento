<?php
namespace Magebees\Blog\Controller\Adminhtml\Tag;
class MassDelete extends \Magento\Backend\App\Action
{	
	protected $tag;	     
	protected $helper;
     public function __construct(
        \Magento\Backend\App\Action\Context $context,
    	\Magebees\Blog\Model\Tag $tag,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    	$this->tag = $tag;
		$this->helper = $helper;

    }
 
    public function execute()
    {
       
        $tagIds = $this->getRequest()->getParam('tagIds');
        
        if (!is_array($tagIds) || empty($tagIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($tagIds);
                foreach ($tagIds as $tagId) {
					$data = array();
                    $tag= $this->tag->load($tagId);
					$data['identifier'] = $tag->getIdentifier();
					$tag->delete();
					$this->_eventManager->dispatch('magebees_blog_rewrite_url_delete', ['data' => $data]);
                }
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been deleted.', count($tagIds))
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
