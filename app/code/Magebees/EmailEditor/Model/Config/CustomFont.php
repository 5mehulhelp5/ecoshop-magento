<?php
namespace Magebees\EmailEditor\Model\Config;

class CustomFont implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'arial', 'label' => __('Arial')],
			['value' => 'arial narrow', 'label' => __('Arial Narrow')],				
			['value' => 'Charcoal', 'label' => __('Charcoal')],
			['value' => 'Courier', 'label' => __('Courier')],
			['value' => 'Courier New', 'label' => __('Courier New')],
			['value' => 'comic sans ms', 'label' => __('Comic Sans MS')], 
			['value' => 'monospace', 'label' => __('Monospace')], 
			['value' => 'garamond', 'label' => __('Garamond')], 
			['value' => 'Geneva', 'label' => __('Geneva')], 
			['value' => 'georgia', 'label' => __('Georgia')], 
			['value' => 'helvetica', 'label' => __('Helvetica')], 
			['value' => 'Lucida Console', 'label' => __('Lucida Console')], 
			['value' => 'Lucida Grande', 'label' => __('Lucida Grande')],
			['value' => 'Lucida Sans Unicode', 'label' => __('Lucida Sans Unicode')],
			['value' => 'Monaco', 'label' => __('Monaco')],
			['value' => 'monospace', 'label' => __('Monospace')],
			['value' => 'MS Sans Serif', 'label' => __('MS Sans Serif')],	
			['value' => 'sans-serif', 'label' => __('Sans serif ')],	
			['value' => 'serif', 'label' => __('Serif [times new roman]')],
			['value' => 'tahoma', 'label' => __('Tahoma')],				
			['value' => 'Open Sans', 'label' => __('Open Sans')], 
			 
		];
	}
}