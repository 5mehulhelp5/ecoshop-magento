<?php
namespace Magebees\Finder\Block;

use Magento\Store\Model\ScopeInterface;

class Finder extends \Magento\Framework\View\Element\Template
{
    
    protected $_dropdownsFactory;
    protected $_finderFactory;
    protected $_productsFactory;
    protected $categoryFactory;
    protected $coreRegistry;
    protected $_parentDropdownId = 0;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory,
        \Magebees\Finder\Model\FinderFactory $finderFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_dropdownsFactory = $dropdownsFactory;
        $this->_finderFactory = $finderFactory;
        $this->categoryFactory = $categoryFactory;
        $this->coreRegistry = $coreRegistry;
                                    
        //Set Configuration values
        $this->setEnabled($this->_scopeConfig->getValue('finder/general/enabled', ScopeInterface::SCOPE_STORE));
        $this->setAutosearch($this->_scopeConfig->getValue('finder/general/autosearch', ScopeInterface::SCOPE_STORE));
        $this->setCategoryPageEnabled($this->_scopeConfig->getValue('finder/general/categorypage_enabled', ScopeInterface::SCOPE_STORE));
        $this->setFindBtnText($this->_scopeConfig->getValue('finder/general/find_btn_text', ScopeInterface::SCOPE_STORE));
        $this->setResetBtnText($this->_scopeConfig->getValue('finder/general/reset_btn_text', ScopeInterface::SCOPE_STORE));
        $this->setEnableDrpdwnSearch($this->_scopeConfig->getValue('finder/general/enable_drpdwn_search', ScopeInterface::SCOPE_STORE));
    }
    
    public function getDropdownsCollectionByFinderId($finderId)
    {
        return $this->_dropdownsFactory->create()->getCollection()
                        ->addFieldToFilter('finder_id', $finderId);
    }
    
    public function getFinderById($finderId)
    {
        return $this->_finderFactory->create()->load($finderId);
    }
    
    public function getSearchResultUrl()
    {
        return $this->getUrl('finder/index/search');
    }
        
    public function getOptionValueUrl()
    {
        return $this->getUrl('finder/index/finder');
    }
    
    public function isButtonEnabled()
    {
        if ($this->getFinderById($this->getFinderId())->getSavedValue('current')) {
            return true;
        }
                
        if (($this->getFinderById($this->getFinderId())->getSavedValue('last'))) {
            return true;
        }
        return false;
    }
    
    public function getDropdownValues($dropdown, $cat_id = 0)
    {
        $values   = [];
        $finder =  $this->getFinderById($this->getFinderId());
    
        $parentValueId  = $finder->getSavedValue($this->_parentDropdownId);
        $currentValueId = $finder->getSavedValue($dropdown->getId());
              
        $values = $dropdown->getValues($parentValueId, $currentValueId, $cat_id);
        
        $this->_parentDropdownId = $dropdown->getId();
        return $values;
    }
    
    //get finder ids by category ids
    public function getFinderIdsByCategoryId()
    {
        $category = $this->getCurrentCategory();
        //get current category
        $collection = $this->_finderFactory->create()->getCollection();
        $collection->addFieldToFilter('category_ids', [['finset' => $category->getId()]]);
        $finderIds = array_map([$this,"getFinderIdssArr"], $collection->getData());
        return $finderIds;
    }
    
    public function getFinderIdssArr($element)
    {
        return $element['finder_id'];
    }
    
    public function getCurrentCategory()
    {
        $category = $this->coreRegistry->registry('current_category');
        return $category;
    }
    
    public function getProductsByCategoryId()
    {
        $categoryId = $this->getCurrentCategory()->getId();
        $category = $this->categoryFactory->create()->load($categoryId);
        $skus = $category->getProductCollection()->addAttributeToSelect('sku')
                                         ->getColumnValues('sku');
        return $skus;
    }
}
