<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Productlisting\Block;


class AjaxPagerUrl extends \Magento\Catalog\Block\Product\Widget\Html\Pager
{
	/**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     *
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;
		return $this->getUrl('', $urlParams);
        
    }

    /**
     * Get path
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->_getData('path') ?: '*/*/*';
    }
}