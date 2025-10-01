<?php
namespace Magebees\Blog\Controller\Adminhtml\Tag;
class Massstatus extends \Magento\Backend\App\Action
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
       
		$delivered_status = (int) $this->getRequest()->getPost('status');
        $tagIds = $this->getRequest()->getParam('tagIds');
        
        if (!is_array($tagIds) || empty($tagIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($tagIds);
                foreach ($tagIds as $tagId) {
                    $tag= $this->category->load($tagId)->setIsActive($delivered_status)->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) Status have been Updated.', count($tagIds))
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
