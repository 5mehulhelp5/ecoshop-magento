<?php
namespace Magebees\PocoBase\Model\Config\Source;
class StaticBlocks implements \Magento\Framework\Option\ArrayInterface
{	
	protected $block;
	public function __construct(\Magento\Cms\Model\Block $block) {
		$this->block = $block;
	}
	public function toOptionArray()
	{
		//echo '<pre>';print_r($this->block->getCollection()->getData());die;
		//return $cms_blocks_collection =$this->block->getCollection()->toOptionArray();;
		$staticBlocks = $this->block->getCollection()->getData();
		$static_block_arr = array();
		foreach($staticBlocks as $block):
		$static_block_arr[] = array('value' => $block['identifier'],'label' => $block['title']);
		endforeach;
		return $static_block_arr;
		
		/*
		return [

			['value' =>'0', 'label' => __('No Sticky')],

			['value' =>'1', 'label' => __('Reverce Sticky')],

			['value' =>'2', 'label' => __('Sticky')]

		];
		*/
		
	}
}

