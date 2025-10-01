<?php
namespace Magebees\Blog\Controller\Adminhtml\Category;
class Massstatus extends \Magento\Backend\App\Action
{
    protected $category;
	protected $helper;
	
	
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
       
		$delivered_status = (int) $this->getRequest()->getPost('status');
        $categoryIds = $this->getRequest()->getParam('categoryIds');
        
        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $count=0;
                 $count=count($categoryIds);
                foreach ($categoryIds as $categoryId) {
                    $category= $this->category->load($categoryId)->setIsActive($delivered_status)->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) Status have been Updated.', count($categoryIds))
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
