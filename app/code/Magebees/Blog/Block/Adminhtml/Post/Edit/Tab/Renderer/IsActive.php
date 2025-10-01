<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
class IsActive extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    
    public function render(DataObject $row)
    {
		
		if($row->getIsActive()==1):
			  $cell = '<span class="grid-severity-notice"><span>Enable</span></span>';	
			else:
			  $cell = '<span class="grid-severity-major"><span>Disable</span></span>';
		endif;
		return $cell;
        
    }
}