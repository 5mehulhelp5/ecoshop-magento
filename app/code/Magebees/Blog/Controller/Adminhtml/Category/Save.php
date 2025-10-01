<?php
namespace Magebees\Blog\Controller\Adminhtml\Category;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{
	protected $_collection;
	protected $_post;
	protected $_scopeConfig;
	protected $category;
	protected $_timezoneInterface;	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Category $category,	
	\Magebees\Blog\Model\Post $post,	
	\Magento\Framework\Module\Dir\Reader $reader,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_post = $post;
		$this->_scopeConfig = $_scopeConfig;
		$this->category = $category;
		$this->_timezoneInterface = $timezoneInterface;
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
       	$data=$this->getRequest()->getPost()->toarray();
		
		if($data)
		{
		
			$id = $this->getRequest()->getParam('category_id');
		    if ($id)
			{
                $this->category->load($id);
				 if ($id != $this->category->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }
			
			
			
			if(!$data['identifier'])
			{
				
				$output = preg_replace('!\s+!', ' ', $data['title']); // Replace Multiple Space
			}else{
				$output = preg_replace('!\s+!', ' ', $data['identifier']); // Replace Multiple Space
			}
			$identifier = str_replace(' ', '-', strtolower($output)); // Replaces all spaces with hyphens.
			$identifier = preg_replace('/[^A-Za-z0-9\-]/', '', $identifier); // Removes special chars.
			$data['identifier'] = $identifier;
			
			
			$data['customer_group'] = implode(',',(array)$data['customer_group']);
			$data['store_id'] = implode(',',(array)$data['store_id']);
			$this->category->setData($data);
			try {
					if ($this->category->getCreatedTime() == NULL || $this->category->getUpdateTime() == NULL) 
					{
						$this->category->setCreatedTime($currentDate)
								->setUpdateTime($currentDate);
					} 
					else 
					{
						$this->category->setUpdateTime($currentDate);
					}	
				
				
					$this->category->save();
					$data['category_id'] = $this->category->getCategoryId();
					$this->_eventManager->dispatch('magebees_blog_category_url', ['data' => $data]);
					//$this->_eventManager->dispatch('magebees_blog_url_check', ['data' => $data]);
				
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					$this->_session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('category_id' => $this->category->getCategoryId(), '_current' => true));
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

