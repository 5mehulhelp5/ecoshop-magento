<?php
namespace Magebees\Layerednavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

class RemoveAttribute implements ObserverInterface
{
    protected $productAttributeCollectionFactory;
    protected $layerattributeFactory;
    protected $attributeoptionFactory;
    protected $_scopeConfig;
    protected $cache_type;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CacheTypeListInterface $cache_type
    ) {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->layerattributeFactory = $layerattributeFactory;
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->_scopeConfig=$scopeConfig;
        $this->cache_type = $cache_type;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $params=$observer->getRequest()->getParams();
                $attribute_id=$params['attribute_id'];
        
                $layer_model=$this->layerattributeFactory->create();
                $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                if ($collection->getSize()) {
                    $layer_model->load($attribute_id, 'attribute_id');
                    $layer_model->delete();
                }
                $option_model=$this->attributeoptionFactory->create();
                $collection = $option_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                $option_coll=$collection->getData();
                                
                if (!empty($option_coll)) {
                    foreach ($option_coll as $option) {
                        $option_model->load($option['option_id'], 'option_id');
                        $option_model->delete();
                    }
                }
                $this->cache_type->invalidate('magebees_layerednavigation');
            }
        }
    }
}
