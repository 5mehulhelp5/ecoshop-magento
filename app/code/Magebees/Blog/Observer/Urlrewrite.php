<?php
namespace Magebees\Blog\Observer;
use Magento\Framework\Event\ObserverInterface;
class Urlrewrite implements ObserverInterface
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
	$this->page = $page;
	
	$this->messageManager = $managerInterface;
	}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
      	$data = $observer->getData('data');
		$identifier = $data['identifier'];
		$category_id = null;
		$blog_url_rewrite_id = null;
		$page_id = null;
		$post_id = null;
		$tag_id = null;
		$url_type = null;
		$cat_id = null;
		$blog_category_id = null;
		$blog_post_id = null;
		$blog_tag_id = null;
		$product_id = null;
		
		
		$url_type = 'custom';
		if(isset($data['category_id']))
		{
		$category_id = $data['category_id'];
		$url_type = 'category';
		}
		if(isset($data['post_id']))
		{
		$post_id = $data['post_id'];
		$url_type = 'post';
		}
		if(isset($data['tag_id']))
		{
		$tag_id = $data['tag_id'];
			$url_type = 'tag';
		}
		
		
		
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
		
		$blog_category = $this->blogcategory->getCollection();
		
		if($category_id):
		$blog_category->addFieldToFilter('category_id', array('neq' => $category_id));
		endif;
		
		$blog_category->addFieldToFilter('identifier', array('eq' => $identifier));
		$blog_category_id = $blog_category->getFirstItem()->getCategoryId();
		if($blog_category_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Blog Category.');
		endif;
		
		
		$blog_post = $this->blogpost->getCollection();
		$blog_post->addFieldToFilter('identifier', array('eq' => $identifier));
		if($post_id):
			$blog_post->addFieldToFilter('post_id', array('neq' => $post_id));
		endif;
		
		$blog_post_id = $blog_post->getFirstItem()->getPostId();
		if($blog_post_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the Blog Post.');
		endif;
		
		
		$blog_tag = $this->blogtag->getCollection();
		$blog_tag->addFieldToFilter('identifier', array('eq' => $identifier));
		if($tag_id):
		$blog_tag->addFieldToFilter('tag_id', array('neq' => $tag_id));
		endif;
		$blog_tag_id = $blog_tag->getFirstItem()->getTagId();
		if($blog_tag_id):
		$this->messageManager->addWarning('Same URL Key already Exist in the Blog Tag.');
		endif;
		
		
		$blogurlrewrite = $this->blogurlrewrite->getCollection();
		$blogurlrewrite->addFieldToFilter('new_url', array('eq' => $data['identifier']));
		
		
		$blog_url_rewrite_id = $blogurlrewrite->getFirstItem()->getUrlId();
		if($blog_url_rewrite_id):
		   $this->messageManager->addWarning('Same URL Key already Exist in the URL Rewrite.');
		endif;
		
		
		
		if(($category_id)&&(($page_id) || ($blog_category_id) || ($blog_post_id) || ($blog_tag_id) || ($product_id) || ($cat_id) || ($blog_url_rewrite_id)))
		{
			$categoryModel = $this->blogcategory->load($category_id);
			$newIdentifier = $categoryModel->getIdentifier()."-".$category_id;
			$data['identifier'] = $newIdentifier;
			$categoryModel->setIdentifier($newIdentifier);
			$categoryModel->save();
		}
		
		if(($post_id)&&(($page_id) || ($blog_category_id) || ($blog_post_id) || ($blog_tag_id) || ($product_id) || ($cat_id) || ($blog_url_rewrite_id)))
		{
			$postModel = $this->blogpost->load($post_id);
			$newIdentifier = $postModel->getIdentifier()."-".$post_id;
			$data['identifier'] = $newIdentifier;
			$postModel->setIdentifier($newIdentifier);
			$postModel->save();
		}
		if(($tag_id)&&(($page_id) || ($blog_category_id) || ($blog_post_id) || ($blog_tag_id) || ($product_id) || ($cat_id) || ($blog_url_rewrite_id)))
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
				//$url_data['store_id'] = $data['store_id'];
				$url_data['old_url'] = $data['current_identifier'];
				$url_data['new_url'] = $data['identifier'];
				$url_data['type'] = $url_type;
				$this->blogurlrewrite->setData($url_data);
				$this->blogurlrewrite->save();
				$blog_url_delete = $this->blogurlrewrite->getCollection()
						->addFieldToFilter('old_url', array('eq' => $data['identifier']));
				$blog_url_delete->walk('delete');
				
			}
		}
		return $this;
    }

}
