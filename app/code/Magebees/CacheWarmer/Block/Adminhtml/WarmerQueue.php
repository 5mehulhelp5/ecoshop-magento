<?php

namespace Magebees\CacheWarmer\Block\Adminhtml;

class WarmerQueue extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cachewarmer';
        $this->_blockGroup = 'Magebees_CacheWarmer';
        $this->_headerText = __('FPC Warmer Queue');
        $this->_addButtonLabel = __('Generate Warmer Queue');
        // $this->_addButtonLabel = __('Start Warm Up');
        parent::_construct();
		$this->buttonList->add(
            'unlock',
            [
        'label' => __('Unlock Warm Process'),
        'class' => 'save',
        'onclick' => 'setLocation(\'' . $this->getUrl('magebees_cachewarmer/queue/unlock') . '\')',
        ]
    );
		
		$this->buttonList->update('add', 'onclick', 'setLocation(\'' . $this->getUrl('magebees_cachewarmer/queue/generate') . '\')');
        $this->buttonList->add(
            'warmqueue',
            [
        'label' => __('Start Warm Up'),
        'class' => 'save',
       // 'onclick' => 'setLocation(\'' . $this->getUrl('magebees_cachewarmer/queue/process') . '\')',
        'style' => '    background-color: #ba4000; border-color: #b84002; box-shadow: 0 0 0 1px #007bdb;color: #fff;text-decoration: none;'
            ]
    );
    }
	
	
	
	public function getQueueProcessUrl()
    {
        return $this->getUrl('magebees_cachewarmer/queue/process');
    }

}
