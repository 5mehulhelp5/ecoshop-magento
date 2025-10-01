<?php
namespace Magebees\Responsivebannerslider\Model\ResourceModel\Responsivebannerslider;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\Responsivebannerslider', 'Magebees\Responsivebannerslider\Model\ResourceModel\Responsivebannerslider');
	}
	public function categoryFilter($category) {
		$this->getSelect()->join(
				array('category_table' => $this->getTable('responsivebannerslider_category')),
				'main_table.slidergroup_id = category_table.slidergroup_id',
				array()
				)
				->where('category_table.category_ids = ?', $category);
		return $this;
	}
	public function productFilter($productsku) {
		$this->getSelect()->join(
				array('product_table' => $this->getTable('responsivebannerslider_product')),
				'main_table.slidergroup_id = product_table.slidergroup_id',
				array()
				)
				->where('product_table.product_sku = ?', $productsku);
		return $this;
	}
	public function pageFilter($pageId) {
		$this->getSelect()->join(
				array('page_table' => $this->getTable('responsivebannerslider_page')),
				'main_table.slidergroup_id = page_table.slidergroup_id',
				array()
				)
				->where('page_table.pages = ?', $pageId);
		return $this;
	}
	public function storeFilter($store_id) {
		$this->getSelect()->join(
				array('store_table' => $this->getTable('responsivebannerslider_store')),
				'main_table.slidergroup_id = store_table.slidergroup_id',
				array()
				)
				->where('store_table. store_id = ?', $store_id);
		return $this;
	}
	
	public function toOptionArray()
    {
		$result = array();
		$options = array(array('value' => '', 'label' => 'Please Select'));
        $result = array_merge($options,parent::_toOptionArray('unique_code', 'title'));
		return $result;
    }
	
}
