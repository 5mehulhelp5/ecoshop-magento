<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxsearch\Model\Layer\Search\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Search\Model\QueryFactory;

class CollectionFilter 
{
    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;
    protected $helper;
    protected $_categoryFactory;
     protected $searchbox;

    /**
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        \Magebees\Ajaxsearch\Helper\Data $helper,
        \Magebees\Ajaxsearch\Block\Searchbox $searchbox,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        QueryFactory $queryFactory
    ) {
    
        $this->queryFactory = $queryFactory;
        $this->helper = $helper;
        $this->_categoryFactory = $categoryFactory;
        $this->searchbox = $searchbox;
    }
    public function aroundFilter(
        \Magento\Catalog\Model\Layer\Search\CollectionFilter $subject,
        \Closure $proceed,
        $collection,
        Category $category
    ) {	         
        $proceed($collection, $category);
        $config=$this->searchbox->getConfig();
        if ($config['enable']) {
            $cat_id=$this->helper->getSelectedCategory();
            if ($cat_id) {
                $cat=$this->_categoryFactory->create()->load($cat_id);
                $collection->addCategoryFilter($cat);
            }
        }
        $collection->addSearchFilter($this->queryFactory->get()->getQueryText());
		
    }	
}
