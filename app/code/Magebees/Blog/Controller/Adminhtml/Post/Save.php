<?php
namespace Magebees\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
	protected $_scopeConfig;
    protected $_collection;
    protected $post;
	protected $_timezoneInterface;
    protected $_jsHelper;
	
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magebees\Blog\Model\Post $post,	
	\Magento\Framework\Module\Dir\Reader $reader,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	\Magento\Backend\Helper\Js $jsHelper,
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_scopeConfig = $_scopeConfig;
		$this->post = $post;
		$this->_timezoneInterface = $timezoneInterface;
		$this->_jsHelper = $jsHelper;
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
	
		$featured_image = $this->getRequest()->getFiles()->toarray();
		
		$data=$this->getRequest()->getPost()->toarray();
		
		if($data)
		{
			
			$id = $this->getRequest()->getParam('post_id');
		    if ($id)
			{
                $this->post->load($id);
				 if ($id != $this->post->getPostId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }
			
			
			
			
			
			
			try {
				
					if((isset($data['deletefeatured']) && $data['deletefeatured']=="on"))
					{
						
						if(file_exists($data['featuredImagePath'])):
						unlink($data['featuredImagePath']);
						endif;
						$data['featured_img'] = null;
						unset($data['featuredImagePath']);
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
				
					if (isset($data['links']['tags'])) {
						$tag_ids = $this->_jsHelper->decodeGridSerializedInput($data['links']['tags']);
						$data['tag_ids'] = implode(',',(array)$tag_ids);
					} 
					if (isset($data['links']['category'])) {
						$category_ids = $this->_jsHelper->decodeGridSerializedInput($data['links']['category']);
						$data['category_ids'] = implode(',',(array)$category_ids);
					} 	
					if (isset($data['links']['post'])) {
						$related_post_ids = $this->_jsHelper->decodeGridSerializedInput($data['links']['post']);
						$data['related_post_ids'] = implode(',',(array)$related_post_ids);
						}
					if (isset($data['links']['product'])) {
						$products_id = $this->_jsHelper->decodeGridSerializedInput($data['links']['product']);
						$data['products_id'] = implode(',',(array)$products_id);
					} 
					
					/*if(isset($data['products_id']))
					{
						if($data['products_id'][0]=='on')
						{
						unset($data['products_id'][0]);
						}
						$data['products_id'] = implode(',',(array)$data['products_id']);
					}
					if(isset($data['post_ids']))
					{
						if($data['post_ids'][0]=='on')
						{
						unset($data['post_ids'][0]);
						}
						$data['related_post_ids'] = implode(',',(array)$data['post_ids']);
					}
					if(isset($data['tag_ids']))
					{
						if($data['tag_ids'][0]=='on')
						{
						unset($data['tag_ids'][0]);
						}
						
						$data['tag_ids'] = implode(',',(array)$data['tag_ids']);
					}
					if(isset($data['category_ids']))
					{
						if($data['category_ids'][0]=='on')
						{
						unset($data['category_ids'][0]);
						}
						$data['category_ids'] = implode(',',(array)$data['category_ids']);
					}*/
					if(!$data['position'])
					{
					$data['position'] = 0;
					}
					
					if(isset($data['save_as_draft']) && ($data['save_as_draft']==1))
					{
					$data['publish_time'] = null;
					}else if(!$data['publish_time'])
					{
						$data['publish_time'] = $currentDate;
					}
					if(isset($data['links']))
					{
						unset($data['links']);
						
					}
					if(isset($data['price']))
					{
						unset($data['price']);
						
					}
					$this->post->setData($data);
					if ($this->post->getCreationTime() == NULL && $this->post->getUpdateTime() == NULL) 
					{
						$this->post->setCreationTime($currentDate);
						$this->post->setUpdateTime($currentDate);
					} 
					else 
					{
						$this->post->setUpdateTime($currentDate);
					}	
					
					$this->post->save();

					$data['post_id'] = $this->post->getPostId();
					
					$this->_eventManager->dispatch('magebees_blog_post_url', ['data' => $data]);
					//$this->_eventManager->dispatch('magebees_blog_url_check', ['data' => $data]);
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					$this->_session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('post_id' => $this->post->getPostId(), '_current' => true));
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
