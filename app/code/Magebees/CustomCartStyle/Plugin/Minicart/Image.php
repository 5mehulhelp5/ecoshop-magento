<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magebees\CustomCartStyle\Plugin\Minicart;

class Image
{
    public function aroundGetItemData($subject, $proceed, $item)
	{
		
		$result = $proceed($item);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->create('Magebees\CustomCartStyle\Helper\Data');
		$style = $helper->getMiniCartStyle();/* popup */ 
		if(($style==1)||($style==2)):
		$_imagehelper = $objectManager->create('Magento\Catalog\Helper\Image');
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($result['product_id']);
		$imageDisplayArea = 'mini_cart_product_thumbnail';
		$image_width = '100';
		$image_height = '120';
		$productImage = $_imagehelper->init($product, $imageDisplayArea)
						->constrainOnly(TRUE)
						->keepAspectRatio(false)
						->keepFrame(TRUE)
						->resize($image_width, $image_height);
		
		/* thum url */ 
        if($product->getThumbnail()){
            $result['custom_product_image']['src'] = $productImage->getUrl();
			$result['custom_product_image']['alt'] = $result['product_image']['alt'];
			$result['custom_product_image']['width'] = 100;
			$result['custom_product_image']['height'] = 120;
        }
        else{
			$result['custom_product_image']['src'] = $_imagehelper->getDefaultPlaceholderUrl('image');
			$result['custom_product_image']['alt'] = $product->getName();
			$result['custom_product_image']['width'] = 100;
			$result['custom_product_image']['height'] = 120;
            $result['product_image']['src'];
        }
		
		endif;
		
        return $result;

    }
}
