<?php
namespace Magebees\Blog\Observer;
use Magento\Framework\Event\ObserverInterface;
class Tagurl implements ObserverInterface
{	
	protected $_categoryFactory;
	protected $_productFactory;
	protected $_pageFactory;
	protected $messageManager;
	protected $blogcategory;
	protected $blogpost;
	protected $blogtag;
	protected $blogurlrewrite;
	protected $page;
	public function __construct( 
		\Magebees\Blog\Model\Category $blogcategory,
		\Magebees\Blog\Model\Post $blogpost,
		\Magebees\Blog\Model\Tag $blogtag,
		\Magebees\Blog\Model\UrlRewrite $blogurlrewrite,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
		\Magento\Cms\Model\Page $page,
		\Magento\Framework\Message\ManagerInterface $managerInterface
	) {    
    $this->blogcategory = $blogcategory;
	$this->blogpost = $blogpost;
	$this->blogtag = $blogtag;
	$this->blogurlrewrite = $blogurlrewrite;
	$this->_categoryFactory = $categoryFactory;
	$this->_productFactory = $productFactory;
	$this->messageManager = $managerInterface;
	$this->page = $page;
	}
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
		$data = $observer->getData('data');
		$tag_id = $data['tag_id'];
		$identifier = $data['identifier'];
		
		$cms_pages_collection =$this->page->getCollection()
									->addFieldToFilter('identifier', array('eq' => $identifier))
									->getFirstItem();
		$page_id = $cms_pages_collection->getPageId();
		if($page_id):
			$this->messageManager->addWarning('Same URL Key already Exist in the CMS Page.');
		endif;
		
		/* Check url key exist in the default category or product page */
		$category_coll = $this->_categoryFactory->create()
					->addAttributeToFilter('url_key', $identifier)
					->addAttributeToSelect('*')
					->getFirstItem();
		$cat_id = $category_coll->getId();
		if($cat_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Catalog Category.');
		endif;
		$product_coll = $this->_productFactory->create()
					->addAttributeToFilter('url_path', $identifier)
					->addAttributeToSelect('*')
					->getFirstItem();
		$product_id = $product_coll->getId();
		if($product_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Catalog Products.');
		endif;
		
		$blog_tag_collection = $this->blogtag->getCollection()
						->addFieldToFilter('tag_id', array('neq' => $tag_id))
						->addFieldToFilter('identifier', array('eq' => $identifier))
						->getFirstItem();
		$blog_tag_id = $blog_tag_collection->getTagId();
		if($blog_tag_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Blog Tag.');
		endif;
		$blog_post_collection = $this->blogpost->getCollection()
						->addFieldToFilter('identifier', array('eq' => $identifier))
						->getFirstItem();
		$blog_post_id = $blog_post_collection->getPostId();
		if($blog_post_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Blog Post.');
		endif;
		
		$blog_category = $this->blogcategory->getCollection()
									->addFieldToFilter('identifier', array('eq' => $identifier))
									->getFirstItem();
		$blog_category_id = $blog_category->getCategoryId();
		if($blog_category_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Blog Category.');
		endif;
		
		
		$blogurlrewrite = $this->blogurlrewrite->getCollection();
		$blogurlrewrite->addFieldToFilter('new_url', array('eq' => $identifier));
		$blogurlrewrite->addFieldToFilter('type', array('neq' => 'tag'));
		$blog_url_rewrite_id = $blogurlrewrite->getFirstItem()->getUrlId();
		if($blog_url_rewrite_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the URL Rewrite.');
		endif;
		
		
		if(($blog_category_id) || ($blog_post_id) || ($blog_tag_id) || ($product_id) || ($cat_id) || ($page_id) || ($blog_url_rewrite_id))
		{
			$tagModel = $this->blogtag->load($tag_id);
			$newIdentifier = $tagModel->getIdentifier()."-".$tag_id;
			$data['identifier'] = $newIdentifier;
			$tagModel->setIdentifier($newIdentifier);
			$tagModel->save();
		}
		
		if(isset($data['current_identifier']) && isset($data['identifier']))
		{
			if($data['current_identifier'] != $data['identifier'])
			{
				$url_data = array();
				//$url_data['store_id'] = 0;
				$url_data['old_url'] = $data['current_identifier'];
				$url_data['new_url'] = $data['identifier'];
				$url_data['type'] = 'tag';
				
				$this->blogurlrewrite->setData($url_data);
				$this->blogurlrewrite->save();
				$blog_url_delete = $this->blogurlrewrite->getCollection()->addFieldToFilter('old_url', array('eq' => $data['identifier']));
				$blog_url_delete->walk('delete');
				
			}
		}	
		
		return $this;
	}
}