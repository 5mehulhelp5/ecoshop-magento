<?php
namespace  Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Gallery extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * Get the after element html.
     *
     * @return mixed
     */
    public function getElementHtml()
    {
    
        $mappingDiv = '<div id="condition_mapping" class="condition-mapping-content"></div>';
        return $mappingDiv;
    }
    
}
