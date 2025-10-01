<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Model\ResourceModel\Productlabel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Productlabel\Model\Productlabel', 'Magebees\Productlabel\Model\ResourceModel\Productlabel');
    }
    
    public function categoryFilter()
    {
        $this->getSelect()->joinLeft(
            ['category' => $this->getTable('magebees_productlabel_category')],
            'main_table.label_id = category.label_id',
            'category.category_ids'
        );
        return $this;
    }
    
    public function productFilter()
    {
        $this->getSelect()->joinLeft(
            ['product' => $this->getTable('magebees_productlabel_product')],
            'main_table.label_id = product.label_id',
            'product.product_sku'
        );
        return $this;
    }
}
