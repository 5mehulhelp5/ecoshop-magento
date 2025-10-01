<?php
/**
 * Copyright © 2021 Magebees. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Magebees\WebImages\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class ImageHelper extends AbstractHelper
{
    const XML_PATH_VECTOR_EXTENSIONS = 'magebees_webimages/extensions/vector';
    const XML_PATH_WEB_IMAGE_EXTENSIONS = 'magebees_webimages/extensions/web_image';
	const XML_PATH_SVG_IMAGE_EXTENSIONS = 'magebees_webimages/extensions/svg_image';

    /**
     * Check if the file is a vector image
     *
     * @param $file
     * @return bool
     */
    public function isVectorImage($file)
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (empty($extension) && file_exists($file)) {
            $mimeType = mime_content_type($file);
            $extension = str_replace('image/', '', $mimeType);
        }

        return in_array($extension, $this->getVectorExtensions());
    }

    /**
     * Get vector image extensions
     *
     * @return array
     */
    public function getVectorExtensions()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VECTOR_EXTENSIONS, 'store') ?: [];
    }

    /**
     * Get web image extensions
     *
     * @return array
     */
    public function getWebImageExtensions()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEB_IMAGE_EXTENSIONS, 'store') ?: [];
    }
	public function getSvgImageExtensions()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SVG_IMAGE_EXTENSIONS, 'store') ?: [];
    }
}
