<?php
namespace Magebees\Finder\Model\ResourceModel;

/**
 * Review resource model
 */
class Finder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_universalproductFactory;
    protected $_productModel;
    protected $_productFactory ;
    
    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_finder', 'finder_id');
    }
    
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magebees\Finder\Model\UniversalProductFactory $universalproductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->_universalproductFactory = $universalproductFactory;
        $this->_productFactory = $productFactory;
    }
    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $id = $object->getId();
        $product_model = $this->_universalproductFactory->create();
        if ($id) {
            $prd_data = $product_model->getCollection()
                            ->addFieldToFilter('finder_id', $id);
            $prd_data->walk('delete');
        }
    
        if ($object->getProductId()) {
            foreach ($object->getProductId() as $product) {
                if ($product) {
                    $data_prd['finder_id'] = $object->getId();
                    $data_prd['product_id'] = $product;
                    $data_prd['sku'] = $this->_productFactory->create()->load($product)->getSku();
                    $product_model->setData($data_prd);
                    $product_model->save();
                }
            }
        }
        
        return parent::_afterSave($object);
    }
}
