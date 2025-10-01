<?php
namespace Magebees\Blog\Block\Adminhtml\Comment\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
    public function render(DataObject $row) {
        if ($row->getStatus() == 1) {
            $cell = '<span class="grid-severity-notice"><span>Approved</span></span>';
        } else if ($row->getStatus() == 0) {
            $cell = '<span class="grid-severity-critical"><span>Pending</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>Not Approved</span></span>';
        }
        return $cell;
    }
}

