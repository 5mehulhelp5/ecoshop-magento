<?php

namespace Magebees\Layerednavigation\Block\Navigation;

class Category extends AbstractRenderLayered
{
    
    protected $_template = 'Magebees_Layerednavigation::layer/category_filter.phtml';
    protected $_scopeConfig; 
    protected $category; 
    protected $_categoryFactory; 
     protected $_objectManager; 
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $category,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {

        $this->_scopeConfig=$context->getScopeConfig();
        $this->category = $category;
        $this->_categoryFactory = $categoryFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    private $htmlPagerBlock;

    public function setHtmlPagerBlock(\Magento\Theme\Block\Html\Pager $htmlPagerBlock)
    {
        $this->htmlPagerBlock = $htmlPagerBlock;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilterItems()
    {
        $items = $this->filter->getItems();
        return $items;
    }
    
    public function getDisplayMode()
    {
        return $display_mode=$this->_scopeConfig->getValue('layerednavigation/category_filter/display_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function CheckParentCategory($cat_id)
    {
        $categories = $this->category->getStoreCategories();
        foreach ($categories as $category) {
               // $category=$category->getId();
               // $category=$this->_categoryFactory->create()->load($category);
            if ($cat_id==$category->getId()) {
                return true;
            }
        }
    }
    public function getChildCount($category)
    {
        $child=0;
        $category=$this->_categoryFactory->create()->load($category);
        $subcategories = $category->getChildrenCategories();
        if ($subcategories) {
            foreach ($subcategories as $subcategory) {
                if ($subcategory->getIsActive()) {
                    $collection = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection');
                         $prodCollection = $collection->addCategoryFilter($subcategory);
                         $count=$prodCollection->count();
                    if ($count) {
                           $child++;
                    }
                }
            }
        }
                     return $child;
    }
    public function getCatLevel($cat_id)
    {
        $category=$this->_categoryFactory->create()->load($cat_id);
        return $category->getLevel();
    }
    public function IsMultiselectCategory()
    {
        return $enable_multiselect=$this->_scopeConfig->getValue('layerednavigation/category_filter/enable_multiselect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    
    public function getDefaultFilterItemUrl($item)
    {
         
        $filter = $item->getFilter();
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        
        if ($requestParameters) {
            $requestParameters =$requestParameters .','.$item->getValue();
        } else {
            $requestParameters =$item->getValue();
        }
        $query = [
          $filter->getRequestVar() => $requestParameters,
          $this->htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
    public function getFilterRemoveUrl($item)
    {
        
          $filter = $item->getFilter();
         $rat_label=$this->_scopeConfig->getValue('layerednavigation/rating_filter/label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        if ($requestParameters) {
            if (strpos($requestParameters, ',')===false) {
                $requestParameters = [$requestParameters];
            } else {
                $requestParameters = explode(',', (string)$requestParameters);
            }
        } else {
             $requestParameters =[];
        }
        foreach ($requestParameters as $key => $value) {
            /*check if price attribute then set remove element*/
            if (($item->getName()=='Price') || ($item->getName()==$rat_label)) {
                $item_value=$item->getValue();
                $val_str=implode("-", $item_value);
                if ($val_str==$value) {
                    unset($requestParameters[$key]);
                }
            }
            
            if ($value == $item->getValue()) {
                unset($requestParameters[$key]);
            }
        }
        if (!empty($requestParameters)) {
            $requestParameters=implode(" ", $requestParameters);
        }
        $query = [
            $filter->getRequestVar() =>$requestParameters
           
        ];
        
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
