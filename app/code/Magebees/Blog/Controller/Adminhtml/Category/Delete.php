<?php
namespace Magebees\Blog\Controller\Adminhtml\Category;

class Delete extends \Magento\Backend\App\Action
{
   
	protected $helper;
	protected $category;
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magebees\Blog\Model\Category $category,
		\Magebees\Blog\Helper\Data $helper
    ) {
    
        parent::__construct($context);
    	$this->category = $category;
		$this->helper = $helper;
    }
    public function execute()
    {
        $category_id = $this->getRequest()->getParam('category_id');
        try {	
				$data = array();
                $category = $this->category->load($category_id);
				$data['identifier'] = $category->getIdentifier();
				$category->delete();
				$this->_eventManager->dispatch('magebees_blog_rewrite_url_delete', ['data' => $data]);
                $this->messageManager->addSuccess(
                    __('Category was deleted successfully!')
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
