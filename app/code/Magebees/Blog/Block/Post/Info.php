<?php
namespace Magebees\Blog\Block\Post;
class Info extends \Magebees\Blog\Block\Post\View
{
	
   public function EnablePublishDate()
	{
		return $post_publication_date = $this->configuration->getConfig('blog/date/post_publication_date');
		
	}
	public function EnableAuthorDisplay()
	{
		return $show_author = $this->configuration->getConfig('blog/author/enabled');
		
	}
	public function EnableAuthorLink()
	{
		return $add_author_link = $this->configuration->getConfig('blog/author/page_enabled');
		
	}
	public function getPostAuthorInfo()
	{
		
		
		if(($this->post_id)&& ($this->_currentPost->getAuthorId())):
		$author_Id = $this->_currentPost->getAuthorId();
		$authorInfo = array();
		$user = $this->_userFactory->create();
        $user->load($author_Id);
		if($user->getUserId()):
		$authorInfo['name'] = $user->getFirstName(). " ".$user->getLastName();
		$author_path = $user->getUsername();
		$fname = $user->getFirstName();
		$lname = $user->getLastName();
		$authorInfo['url'] = $this->_blogurl->getBlogAuthorurl($fname,$lname);
		endif;
		
		return $authorInfo;
		endif;
		return null;
		
		
	}
	
	public function getCreationDate()
	{
			$post_publication_date_format = $this->configuration->getConfig('blog/date/post_publication_date_format');
			if(!$post_publication_date_format):
				$post_publication_date_format = 'F d, Y H:i:s';
			endif;
		if(($this->post_id)&& ($this->_currentPost)): 
			$publish_time = $this->_currentPost->getCreationTime();
		return $this->_timezoneInterface->date($publish_time)->format($post_publication_date_format);
		
		endif;
		return null;	
	
	}
	
	public function getPostTags()
	{
		if(($this->post_id)&& ($this->_currentPost)):
		$posttagIds = $this->_currentPost->getTagIds();
		$storeId = $this->_storemanager->getStore()->getId();
		$posttagcollection = $this->_blogtag->getCollection();
		$posttagcollection->addFieldToFilter('tag_id', array('in' => $posttagIds));
		return $posttagcollection;
		endif;
		return null;
		
		
	}
	public function getTagurl($tag){
		return $this->_blogurl->getBlogTagurl($tag);
	
	}
	public function getCategoriesCount()
	{
		if(($this->post_id)&& ($this->_currentPost)):
			
			$postCategoryIds = $this->_currentPost->getCategoryIds();
		
		$storeId = $this->_storemanager->getStore()->getId();
		$postCategoriescollection = $this->_blogcategory->getCollection();
		$postCategoriescollection->addFieldToFilter('category_id', array('in' => $postCategoryIds));
		$postCategoriescollection->addFieldToFilter('is_active', array('eq' => 1));
		$postCategoriescollection->addFieldToFilter(['store_id','store_id'],[['eq' => 0],['finset'=>$storeId]]);
		return $postCategoriescollection->getSize();
		endif;
		return null;
		
		
	}
	
	public function getPostCommentsCount()
	{
	if(($this->post_id)&& ($this->_currentPost)):
		$collection = $this->_blogcomment->getCollection();
		$collection->addFieldToFilter('post_id', array('eq'=> $this->post_id));
		$collection->addFieldToFilter('parent_id', array('eq'=> 0));
		$collection->addFieldToFilter('status', array('eq'=> 1));
		return	$collection->getSize();
	endif;
	return null;
	}
	public function getPostCategories()
	{	
		
		if(($this->post_id)&& ($this->_currentPost)):
			
			$postCategoryIds = $this->_currentPost->getCategoryIds();
		
			
		$blog_route_key = $this->configuration->getBlogRoute();
		$storeId = $this->_storemanager->getStore()->getId();
		$parentCategoryurl = $this->configuration->getConfig('blog/permalink/category_use_categories');
		$category_route = $this->configuration->getBlogCategoryRoute();
		$category_sufix = $this->configuration->getConfig('blog/permalink/category_sufix');
		
		$postCategoriescollection = $this->_blogcategory->getCollection();
		$postCategoriescollection->addFieldToFilter('category_id', array('in' => $postCategoryIds));
		$postCategoriescollection->addFieldToFilter('is_active', array('eq' => 1));
		$postCategoriescollection->addFieldToFilter(['store_id','store_id'],[['finset' => 0],['finset'=>$storeId]]);
		$postCategoryList = array();
		foreach($postCategoriescollection as $category):
		
			//$category_url = $this->_bloghelper->getBlogCategoryurl($category,$blog_route_key,$category_sufix,$parentCategoryurl,$category_route);
		//getBlogCategoryurl($category)
		$category_url = $this->_blogurl->getBlogCategoryurl($category);
		$postCategoryList[] = array('title'=>$category->getTitle(),'url'=>$category_url);
		endforeach;
		return $postCategoryList;
		
		endif;
		return null;
		
		
		
	}
    
}

