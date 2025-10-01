<?php
namespace Magebees\Blog\Controller\Adminhtml\Urlrewrite;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{
	protected $_collection;
	protected $_Urlrewrite;
	protected $_scopeConfig;
	protected $category;
	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Category $category,	
	\Magebees\Blog\Model\UrlRewrite $Urlrewrite,	
	\Magento\Framework\Module\Dir\Reader $reader,
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_Urlrewrite = $Urlrewrite;
		$this->_scopeConfig = $_scopeConfig;
		$this->category = $category;
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		$data=$this->getRequest()->getPost()->toarray();
		if($data)
		{
		
			$id = $this->getRequest()->getParam('url_id');
		    if ($id)
			{
                $this->_Urlrewrite->load($id);
				 if ($id != $this->_Urlrewrite->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
				
				
				
				
				
            }
			
			try {
				
					$blog_url_dublicate = $this->_Urlrewrite->getCollection();
					if($id)
					{
					$blog_url_dublicate->addFieldToFilter('url_id', array('neq' => $id));				
					}
					$blog_url_dublicate->addFieldToFilter('old_url', array('eq' => $data['old_url']));
					$blog_url_dublicate->addFieldToFilter('new_url', array('eq' => $data['new_url']));
					if($blog_url_dublicate->getSize() > 0)
					{
					$this->messageManager->addWarning(__('The Record already existed.'));	
					$this->_redirect('*/*/');
					return;
					}else{
					$this->_Urlrewrite->setData($data);	
					$this->_Urlrewrite->save();
						
					
						$blog_url_delete = $this->_Urlrewrite->getCollection()
							->addFieldToFilter('url_id', array('neq' => $id))
							->addFieldToFilter('old_url', array('eq' => $data['new_url']));
						$blog_url_delete->walk('delete');
						
						
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					}
					
					
					$this->_session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('url_id' => $this->_Urlrewrite->getUrlId(), '_current' => true));
						return;
					}
					$this->_redirect('*/*/');
					return;
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

