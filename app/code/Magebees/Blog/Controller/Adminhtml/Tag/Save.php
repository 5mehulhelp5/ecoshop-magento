<?php
namespace Magebees\Blog\Controller\Adminhtml\Tag;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{
	protected $_collection;
	protected $_scopeConfig;	     
	protected $tag;
	protected $_timezoneInterface;	        
	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Tag $tag,	
	\Magento\Framework\Module\Dir\Reader $reader,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_scopeConfig = $_scopeConfig;
		$this->tag = $tag;
		$this->_timezoneInterface = $timezoneInterface;
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
       	$data=$this->getRequest()->getPost()->toarray();
		
		if($data)
		{
			$id = $this->getRequest()->getParam('tag_id');
		    if ($id)
			{
                $this->tag->load($id);
				 if ($id != $this->tag->getTagId()) {
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
			$this->tag->setData($data);
			try {
					if ($this->tag->getCreatedTime() == NULL || $this->tag->getUpdateTime() == NULL) 
					{
						$this->tag->setCreatedTime($currentDate)
								->setUpdateTime($currentDate);
					} 
					else 
					{
						$this->tag->setUpdateTime($currentDate);
					}	
					
					$this->tag->save();
					$data['tag_id'] = $this->tag->getTagId();
					$this->_eventManager->dispatch('magebees_blog_tag_url', ['data' => $data]);
					//$this->_eventManager->dispatch('magebees_blog_url_check', ['data' => $data]);
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					$this->_session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('tag_id' => $this->tag->getTagId(), '_current' => true));
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
