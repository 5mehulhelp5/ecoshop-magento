<?php
namespace Magebees\Blog\Block\Adminhtml\Tag\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
class IsImported extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    
    public function render(DataObject $row)
    {
		
		if($row->getIsImported()==1):
			  $cell = '<span class="grid-severity-notice"><span>Is Imported</span></span>';	
			else:
			  $cell = '<span class="grid-severity-major"><span>Is Created</span></span>';
		endif;
		return $cell;
        
    }
}
