<?php

namespace Magebees\CacheWarmer\Block\Adminhtml\Warmerreport\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cachewarmer_report_tabs');
        $this->setDestElementId('status_report');
        $this->setTitle(__('FPC Reports'));
    }
    protected function _prepareLayout()
    {
        
            
        $this->addTab(
            'report_status',
            [
                'label' => __('Warmed Pages Status'),
                'title' => __('Warmed Pages Status'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\CacheWarmer\Block\Adminhtml\Warmerreport\Edit\Tab\StatusReport'
                )->toHtml()
            ]
        );
        $this->addTab(
            'report_page',
            [
                'label' => __('Warmed Pages'),
                'title' => __('Warmed Pages'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\CacheWarmer\Block\Adminhtml\Warmerreport\Edit\Tab\PageReport'
                )->toHtml()
            ]
        );
        return parent::_prepareLayout();
    }
}
