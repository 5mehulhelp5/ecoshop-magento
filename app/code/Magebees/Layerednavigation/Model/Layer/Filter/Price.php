<?php
namespace Magebees\Layerednavigation\Model\Layer\Filter;

class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
{
   
    private $custSession;
    private $priceDataProvider;
    private $layerHelper;
    private $fromValue;
    private $toValue;
    private $priceCurrency;
    protected $_scopeConfig;
    
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );
        $this->priceCurrency = $priceCurrency;
        $this->custSession = $customerSession;
        $this->_scopeConfig=$scopeConfig;
        $this->priceDataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
          $this->layerHelper = $layerHelper;
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        
        $attribute = $this->getAttributeModel();
        $price_filter_config=$this->_scopeConfig->getValue('layerednavigation/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                if (($price_filter_config['price_type']=='1') || ($price_filter_config['price_type']=='4')) {
                     $filter = $request->getParam($this->getRequestVar());
                    if (!$filter || is_array($filter)) {
                        return $this;
                    }

                     $filterParams = explode(',',  (string)$filter);
                     $filter = $this->priceDataProvider->validateFilter($filterParams[0]);
                    if (!$filter) {
                        return $this;
                    }

                     list($from, $to) = $filter;
                     $this->fromValue = $from;
                     $this->toValue = $to;

                       /*  $this->getLayer()->getProductCollection()->addFieldToFilter(
                         'price',
                         ['from' => $from, 'to' =>  empty($to) || $from == $to ? $to : $to - self::PRICE_DELTA]
                     );*/
                     $this->getLayer()->getProductCollection()->addFieldToFilter(
                         'price',
                         ['from' => $from, 'to' => $to]
                     );
           
                         $this->getLayer()->getState()->addFilter(
                             $this->_createItem($this->_renderLabel(empty($from) ? 0 : $from, $to), $filter)
                         );

                         return $this;
                }
            } else {
                return parent::apply($request);
            }
        } else {
             return parent::apply($request);
        }
        return parent::apply($request);
    }

    public function _renderLabel($fromPrice, $toPrice)
    {
        
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
           /* if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }*/

            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _initItems()
    {
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $layer = $this->getLayer();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $layer->getProductCollection();
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $attribute = $this->getAttributeModel();
                $price_filter_config=$this->_scopeConfig->getValue('layerednavigation/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if (($price_filter_config['price_type']=='1') || ($price_filter_config['price_type']=='4')) {
                    if (!$this->_items) {
                        $attributeCode = $attribute->getAttributeCode();               
                        if ($this->layerHelper->isFilterApplied($layer->getState(), $attributeCode)) {
                          //  $productCollection = $layer->getCurrentCategory()->getProductCollection();
                        }
                        $productCollection->addPriceData(
                            $this->custSession->getCustomerGroupId(),
                            $this->_storeManager->getStore()->getWebsiteId()
                        );
                        $minValue = $productCollection->getMinPrice();
                        $maxValue = $productCollection->getMaxPrice();
                        if (!$this->fromValue) {
                                   $this->fromValue = $minValue;
                        }
                        if (!$this->toValue) {
                                    $this->toValue = $maxValue;
                        }

                        if ($minValue == $maxValue) {
                                    $this->_items = [];
                        } else {
                                    $this->_items = [
                                        'min' => $minValue,
                                        'from' => $this->fromValue,
                                        'to' => $this->toValue,
                                        'max' => $maxValue,
                                    ];
                        }
                    }
                     return $this;
                } else {
                    $this->renderFilterAvail();                 
                }
            } else {
                $this->renderFilterAvail();                 
            }
        } else {
             $this->renderFilterAvail();                    
        }
       $this->renderFilterAvail();                  
    }
    public function renderFilterAvail()
    {
        $attribute = $this->getAttributeModel(); 
        $productCollection = $this->getLayer()->getProductCollection();
        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());
        if(!empty($facets))
                    {                       
                    return parent::_initItems();
                    }
                    else
                    {
                        $this->_items = [];
                        return $this;
                    }
        //return $facets;
    }
}
