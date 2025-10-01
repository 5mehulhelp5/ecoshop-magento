<?php
namespace Magebees\Productlisting\Model\ResourceModel;

/**
 * Review resource model
 */
class Productlisting extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_selectproductFactory;
	protected $_productFactory;
	
	/**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magebees_productlisting', 'listing_id');
    }
	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magebees\Productlisting\Model\SelectProductFactory $selectproductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->_selectproductFactory = $selectproductFactory;
        $this->_productFactory = $productFactory;
    }
    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $id = $object->getId();
        $product_model = $this->_selectproductFactory->create();
        if ($id) {
            $prd_data = $product_model->getCollection()
                            ->addFieldToFilter('listing_id', $id);
            $prd_data->walk('delete');
        }
		//print_r($object->getData());exit;
        if ($object->getProducts()) {
            foreach ($object->getProducts() as $product) {
                if ($product) {
                    $data_prd['listing_id'] = $object->getId();
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
