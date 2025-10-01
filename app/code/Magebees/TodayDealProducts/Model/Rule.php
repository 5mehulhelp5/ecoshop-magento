<?php
namespace Magebees\TodayDealProducts\Model;

use Magento\Catalog\Model\Product;

class Rule extends \Magento\CatalogRule\Model\Rule
{
    protected $_product;
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magebees\TodayDealProducts\Model\ResourceModel\Deal');
        $this->setIdFieldName('entity_id');
    }

    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_productsFilter = $product->getId();
    }

    public function getMatchingProductIds() //skip afterGetMatchingProductIds plugin
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);


            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->_productCollectionFactory->create();
            if ($this->_productsFilter) {
                $productCollection->addIdFilter($this->_productsFilter);
            }

            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->_resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateDealProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->_productFactory->create()
                ]
            );
        }
        return $this->_productIds;
    }

    public function callbackValidateDealProduct($args)
    {
        $product = $args['product'];        $product->setData($args['row']);        $stores = $this->getStores();        $stores = explode(',', (string)$stores);
        $results = [];
        foreach ($stores as $storeId) {
            $product->setStoreId($storeId);
            $validate = $this->getConditions()->validate($product);
            if ($validate) {
                $results[$storeId] = $validate;
                $this->_productIds[$product->getId()] = $results;
            }
        }
    }
}
