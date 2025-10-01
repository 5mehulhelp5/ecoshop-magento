<?php
namespace Magebees\Layerednavigation\Model\Plugin;

class FilterRenderer
{
    
    private $layout;

    /**class name of the block for magebees range slider attributes.*/
    
    private $sliderBlock = 'Magebees\Layerednavigation\Block\Navigation\RangeSlider';

/**class name of the block for magebees from - to type attributes.*/
    private $fromToBlock = 'Magebees\Layerednavigation\Block\Navigation\FromTo';
   /**class name of the block for magebees multi select attributes.*/
    
    private $multiSelectBlock = 'Magebees\Layerednavigation\Block\Navigation\MultiSelect';
    
    /**class name of the block for magebees dropdown attributes.*/
    private $dropDownBlock = 'Magebees\Layerednavigation\Block\Navigation\DropDown';
    
    private $categoryBlock='Magebees\Layerednavigation\Block\Navigation\Category';
    
    private $defaultFilterBlock = 'Magebees\Layerednavigation\Block\Navigation\Filter';

    private $layerHelper;
    protected $_scopeConfig;


   
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory
    ) {
        $this->layout = $layout;
        $this->_scopeConfig=$scopeConfig;
        $this->layerHelper = $layerHelper;
    }

    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $rating_filter_config=$this->_scopeConfig->getValue('layerednavigation/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $stock_filter_config=$this->_scopeConfig->getValue('layerednavigation/stock_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $price_filter_config=$this->_scopeConfig->getValue('layerednavigation/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $cat_filter_config=$this->_scopeConfig->getValue('layerednavigation/category_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    
                if ($filter->hasAttributeModel()) {
                    if (($filter instanceof \Magento\CatalogSearch\Model\Layer\Filter\Price)||($filter instanceof \Magento\Catalog\Model\Layer\Filter\Price)) {
                          $attribute_code=$this->layerHelper->getAttributeCode($filter->getAttributeModel());
                          $price_from_to=$price_filter_config['price_from_to'];
                        $show_product_count=$this->layerHelper->isShowProductCountPrice();
                        $unfold_option_price=$this->layerHelper->unfoldOptionPrice();
                        if ($price_filter_config['price_type']=='1') {
                         // for input type range slider
                            return $this->layout
                            ->createBlock($this->sliderBlock)
                            ->setMagebeesNavFilter($filter)
                            ->toHtml();
                        } elseif ($price_filter_config['price_type']=='2') {
                             //input type dropdown
                             return $this->layout
                            ->createBlock($this->dropDownBlock)
                            ->setAttributeCode($attribute_code)
                            ->setMagebeesNavFilter($filter)
                            ->setFromToWidget($price_from_to)
                            ->setPriceBlock('1')
                            ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                            ->toHtml();
                        } elseif ($price_filter_config['price_type']=='3') {
                         //input type from-to
                             return $this->layout
                            ->createBlock($this->fromToBlock)
                            ->setMagebeesNavFilter($filter)
                            ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                            ->toHtml();
                        } else {
                             return $this->layout
                            ->createBlock($this->defaultFilterBlock)
                            ->setMagebeesNavFilter($filter)
                            ->setAttributeCode($attribute_code)
                            ->setShowProductCount($show_product_count)
                            ->setFromToWidget($price_from_to)
                            ->setPriceBlock('1')
                            ->setUnfoldOption($unfold_option_price)
                            ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                            ->toHtml();
                        }
                    } else {
                        $attr_id=$filter->getAttributeModel()->getId();
                        $unfold_option=$this->layerHelper->getUnfoldedOption($attr_id);
                        $searchbox=$this->layerHelper->isEnableSearchbox($attr_id);
                        $attribute_code=$this->layerHelper->getAttributeCode($filter->getAttributeModel());
              
                        $show_product_count=$this->layerHelper->isDisplayProductCount($attr_id);
            
                        if ($this->layerHelper->isApplyMultiSelect($filter->getAttributeModel()) && !$this->layerHelper->hasVisualSwatch($filter->getAttributeModel())) {
                            return $this->layout
                            ->createBlock($this->multiSelectBlock)
                            ->setMagebeesNavFilter($filter)
                            ->setUnfoldOption($unfold_option)
                            ->setSearchbox($searchbox)
                            ->setAttributeCode($attribute_code)
                            ->setShowProductCount($show_product_count)
                            ->setAttributeId($attr_id)
                            ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                            ->toHtml();
                        } elseif ($this->layerHelper->isApplyDropDown($filter->getAttributeModel())) {
                            if ($this->layerHelper->hasVisualSwatch($filter->getAttributeModel())) {
                                $default_config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            
                                if ($default_config['default_swatch_config']=='0') {
                                    return $this->layout
                                            ->createBlock($this->dropDownBlock)
                                            ->setAttributeCode($attribute_code)
                                            ->setMagebeesNavFilter($filter)
                                            ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                                            ->toHtml();
                                }
                            } else {
                                return $this->layout
                                ->createBlock($this->dropDownBlock)
                                ->setAttributeCode($attribute_code)
                                ->setMagebeesNavFilter($filter)
                                ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                                ->toHtml();
                            }
                        } elseif (!$this->layerHelper->hasVisualSwatch($filter->getAttributeModel())) {
                            return $this->layout
                              ->createBlock($this->defaultFilterBlock)
                              ->setMagebeesNavFilter($filter)
                              ->setUnfoldOption($unfold_option)
                              ->setSearchbox($searchbox)
                              ->setShowProductCount($show_product_count)
                              ->setAttributeCode($attribute_code)
                              ->setAttributeId($attr_id)
                              ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                              ->toHtml();
                        }
                    }
                } else {
                    if ($filter instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Category) {
                        $show_product_count=$cat_filter_config['show_count'];
                
                          return $this->layout
                          ->createBlock($this->categoryBlock)
                          ->setMagebeesNavFilter($filter)
                          ->setShowProductCount($show_product_count)
                         ->setHtmlPagerBlock($this->layout->getBlock('product_list_toolbar_pager'))
                          ->toHtml();
                    }
                }
                return $proceed($filter);
            } else {
                return $proceed($filter);
            }
        } else {
            return $proceed($filter);
        }
    }
}
