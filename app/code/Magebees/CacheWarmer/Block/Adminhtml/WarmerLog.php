<?php

namespace Magebees\CacheWarmer\Block\Adminhtml;

class WarmerLog extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_warmerlog';
        $this->_blockGroup = 'Magebees_CacheWarmer';
        $this->_headerText = __('FPC Warmer Log');
        $this->_addButtonLabel = __('Clear Log');
        // $this->_addButtonLabel = __('Start Warm Up');
        parent::_construct();
         $this->buttonList->update('add', 'onclick', 'setLocation(\'' . $this->getUrl('magebees_cachewarmer/log/clear') . '\')');
    }
}
