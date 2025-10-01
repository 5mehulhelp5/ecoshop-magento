<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Finder\Block;

class ResultToolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{

    /**
     * Get specified products limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
		$limit = 10;
        return $limit;
    }

    
}
