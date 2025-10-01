<?php

namespace Magebees\Layerednavigation\Block\Navigation;

class RangeSlider extends AbstractRenderLayered
{
    
    private $maxValue;

    /**
     * The minimum value of the attribute.
     *
     * @var float
     */
    private $minValue;

    /**
     * The left value of the attribute slider.
     *
     * @var flaot
     */
    private $leftValue;

    /**
     * The right value of the attribute slider.
     *
     * @var flaot
     */
    private $rightValue;

    protected $objectManager; 
    protected $itemCollectionProvider; 
    protected $request; 
    protected $_scopeConfig; 

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $itemCollectionProvider,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
            parent::__construct($context);
             $this->objectManager = $objectManager;
             $this->itemCollectionProvider = $itemCollectionProvider;
             $this->request =$context->getRequest();
             $this->_scopeConfig=$context->getScopeConfig();
             $price_filter_config=$this->_scopeConfig->getValue('layerednavigation/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($price_filter_config['price_type']=='1') {
            if ($price_filter_config['price_from_to']=='1') {
                $this->setTemplate('Magebees_Layerednavigation::layer/slider_fromto.phtml');
            } else {
                 $this->setTemplate('Magebees_Layerednavigation::layer/rangeslider.phtml');
            }
        }
    }
     
     
      /**
       * Returns the minimum value of the attribute for the current product collection.
       *
       * @return float
       */
    public function getMinValue()
    {
        $items = $this->filter->getItems();
        return $items['min'];
    }

    /**
     * Returns the maximum value of the attribute for the current product collection.
     *
     * @return float
     */
    public function getMaxValue()
    {
        $items = $this->filter->getItems();
        return $items['max'];
    }

    /**
     * Returns the left value of the slider.
     *
     * @return float
     */
    public function getLeftValue()
    {
        $items = $this->filter->getItems();
        return $items['from'];
    }

    /**
     * Returns the right value of the slider.
     *
     * @return flaot
     */
    public function getRightValue()
    {
        $items = $this->filter->getItems();
        return $items['to'];
    }

     
    public function getFilterItems()
    {
        return $this->filter->getItems();
    }
    
    public function getFilterUrl()
    {
            
        if (strpos($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]), 'price') !== false) {
            $query = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
            return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        } else {
             return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        }
    }
    public function getPriceRange(){
        
        $filter=$this->filter;
        $Filterprice = array('min' => 0 , 'max'=>0);
       // if($filter instanceof Magento\CatalogSearch\Model\Layer\Filter\Price){
            $priceArr = $filter->getResource()->loadPrices(10000000000);
            $Filterprice['min'] = reset($priceArr);
            $Filterprice['max'] = end($priceArr);
       // }
        return $Filterprice;
    }
  
    public function getRemoveUrl()
    {
        $query = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
