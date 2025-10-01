<?php
namespace Magebees\Navigationmenu\Block;
use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Framework\Registry;

class Breadcrumbs extends \Magento\Framework\View\Element\Template
{

    /**
     * Catalog data
     *
     * @var Data
     */
    protected $_catalogData = null;
	protected $registry;
	protected $redirect;
	protected $categoryFactory;
    /**
     * @param Context $context
     * @param Data $catalogData
     * @param array $data
     */
    public function __construct(
		Context $context, 
		Data $catalogData, 
		Registry $registry,
		array $data = [])
    {
        $this->_catalogData = $catalogData;	
		$this->registry = $registry;
		parent::__construct($context, $data);
    }

	public function getCrumbs()
    {
		
		
		$evercrumbs = array();
		
		$evercrumbs[] = array(
			'label' => 'Home',
			'title' => 'Go to Home Page',
			'link' => $this->_storeManager->getStore()->getBaseUrl()
		);
		
		
		
		
		//print_r($_SERVER['HTTP_REFERER']);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$redirect = $objectManager->create('Magento\Framework\App\Response\RedirectInterface');
		$categoryFactory = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
		$categoryModel = $objectManager->create('Magebees\Navigationmenu\Model\Category');
		$redirectUrl = $redirect->getRefererUrl();
		
		$category_path = explode('/',$redirectUrl);
		
		if(isset($category_path[0]))
		{
			unset($category_path[0]);
		}
		if(isset($category_path[1]))
		{
			unset($category_path[1]);
		}
		if(isset($category_path[2]))
		{
			unset($category_path[2]);
		}
		
		array_filter($category_path);
		foreach($category_path as $path):
			$path_explode = explode(".",$path);
			if(isset($path_explode[0])){
				$path = $path_explode[0];
			}
			
			$category = $categoryFactory->create()->getCollection()->addAttributeToFilter('url_key',$path)->getFirstItem();
			$categoryId = $category->getId();
			if($categoryId):
			$category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
			$parentCategories = $category->getParentIds();
			foreach ($parentCategories as $pcatId){
				$parent_category = $objectManager->create('Magento\Catalog\Model\Category')->load($pcatId);	
				$isAvailable = $categoryModel->checkCategoryAvailable($pcatId);
				if(($parent_category->getLevel() > 1)&&($isAvailable)){

					$evercrumbs[$pcatId] = array(
							'label' => $parent_category->getName(),
							'title' => $parent_category->getName(),
							'link' => $parent_category->getUrl()
						);
				}
				
						
			}
			
			$evercrumbs[$categoryId] = array(
							'label' => $category->getName(),
							'title' => $category->getName(),
							'link' => $category->getUrl()
						);
						
			endif;
		endforeach;
		
		
		$product = $this->registry->registry('current_product');
		$evercrumbs[] = array(
				'label' => $product->getName(),
				'title' => $product->getName(),
				'link' => ''
			);
				
		return $evercrumbs;
   
   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
   $category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
   $category_id = $category->getId();
  
		$path = $this->_catalogData->getBreadcrumbPath();
		$product = $this->registry->registry('current_product');
		$categoryCollection = clone $product->getCategoryCollection();
		
		$categoryCollection->clear();
		$categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)->addAttributeToFilter('path', array('like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"));
		foreach($categoryCollection as $cat):
			if($category_id==$cat->getId()):
				$breadcrumbCategories = $cat->getParentCategories();
				foreach ($breadcrumbCategories as $category) {
			$evercrumbs[] = array(
				'label' => $category->getName(),
				'title' => $category->getName(),
				'link' => $category->getUrl()
			);
		}
			endif;
		endforeach;
		
		$evercrumbs[] = array(
				'label' => $product->getName(),
				'title' => $product->getName(),
				'link' => ''
			);
				
		return $evercrumbs;
    }
}
