<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\Plugin;

class Layer
{
    /**
     * Stock status instance
     *
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * Store config instance
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $request;
    protected $_stockResource;
    protected $layerHelper;

    /**
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
         \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->stockHelper = $stockHelper;
        $this->scopeConfig = $scopeConfig;
         $this->_stockResource = $stockResource;
         $this->request = $request;
          $this->layerHelper = $layerHelper;
    }

    /**
     * Before prepare product collection handler
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
    ) {
          $IsElasticSearchEnabled=$this->layerHelper->IsElasticSearch();  
        if($IsElasticSearchEnabled)
        {
             return;
        }
        if ($this->_isEnabledShowOutOfStock()) {
            $is_enabled=$this->scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $is_default_enabled=$this->scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($is_default_enabled==0) {
                if ($is_enabled) {
                    $stockFlag = 'has_stock_status_filter';
                     $stock_requestVar = $this->scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                     $stock_filter = $this->request->getParam($stock_requestVar, null);
                    if (!$collection->hasFlag($stockFlag)) {
                        $isShowOutOfStock = $this->scopeConfig->getValue(
                            \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
                        $this->_stockResource->addIsOutStockFilterToCollection($collection, $stock_filter);
                        $collection->setFlag($stockFlag, true);
                    }
                } else {
                    return;
                }
            } else {
                return;
            }
        } else {
            $this->stockHelper->addIsInStockFilterToCollection($collection);
        }
    }

    /**
     * Get config value for 'display out of stock' option
     *
     * @return bool
     */
    protected function _isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
