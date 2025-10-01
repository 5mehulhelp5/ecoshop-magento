<?php

namespace Magebees\PocoBase\Block\Widget;

use Magento\Widget\Block\BlockInterface;

class Post extends \Magento\Framework\View\Element\Template implements BlockInterface
{
	//replcace \FishPig\WordPress\Block\Sidebar\Widget\Posts
    public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
    
}


