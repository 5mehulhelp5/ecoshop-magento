<?php
namespace Magebees\Blog\Controller\Adminhtml\Tag;

class Delete extends \Magento\Backend\App\Action
{
   protected $helper;
   protected $tag;	     
	
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
        $tag_id = $this->getRequest()->getParam('tag_id');
        try {
				$data = array();
                $tag = $this->tag->load($tag_id);
				$data['identifier'] = $tag->getIdentifier();
				$tag->delete();
				$this->_eventManager->dispatch('magebees_blog_rewrite_url_delete', ['data' => $data]);
                $this->messageManager->addSuccess(
                    __('Tag was deleted successfully!')
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
