<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\Product;

/**
 * Class Media
 */
class Media extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Catalog\Model\Product Factory
     */
    protected $productModelFactory;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;
    protected $productMetadata;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->productModelFactory = $productModelFactory;
        $this->swatchHelper = $swatchHelper;
        $this->productMetadata = $productMetadata;

        parent::__construct($context);
    }

    /**
     * Get product media for specified configurable product variation
     *
     * @return string
     */
    public function execute()
    {
        $productMedia = [];
        if ($productId = (int)$this->getRequest()->getParam('product_id')) {
            $currentConfigurable = $this->productModelFactory->create()->load($productId);
            $attributes = (array)$this->getRequest()->getParam('attributes');
            if (!empty($attributes)) {
                $product = $this->getProductVariationWithMedia($currentConfigurable, $attributes);
            }
            if ((empty($product) || (!$product->getImage() || $product->getImage() == 'no_selection'))
                && isset($currentConfigurable)
            ) {
                $product = $currentConfigurable;
            }
            $productMedia = $this->swatchHelper->getProductMediaGallery($product);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($productMedia);
        return $resultJson;
    }
    protected function getProductVariationWithMedia(Product $currentConfigurable, array $attributes)
    {
        $product = null;
        $layeredAttributes = [];
        $configurableAttributes = $this->swatchHelper->getAttributesFromConfigurable($currentConfigurable);
        if ($configurableAttributes) {
            $layeredAttributes = $this->getLayeredAttributesIfExists($configurableAttributes);
        }
        $resultAttributes = array_merge($layeredAttributes, $attributes);

        $product = $this->swatchHelper->loadVariationByFallback($currentConfigurable, $resultAttributes);
        if (!$product || (!$product->getImage() || $product->getImage() == 'no_selection')) {
            if (version_compare($this->productMetadata->getVersion(), '2.1.0', '<')) {
                $product = $this->swatchHelper->loadFirstVariationSwatchImage($currentConfigurable, $resultAttributes);
            } else {
                 $product = $this->swatchHelper->loadFirstVariationWithSwatchImage($currentConfigurable, $resultAttributes);
            }
        }
        if (!$product) {
            if (version_compare($this->productMetadata->getVersion(), '2.1.0', '<')) {
                $product = $this->swatchHelper->loadFirstVariationImage($currentConfigurable, $resultAttributes);
            } else {
                  $product = $this->swatchHelper->loadFirstVariationWithImage($currentConfigurable, $resultAttributes);
            }
        }
        return $product;
    }

    /**
     * @param array $configurableAttributes
     * @return array
     */
    protected function getLayeredAttributesIfExists(array $configurableAttributes)
    {
        $layeredAttributes = [];

        foreach ($configurableAttributes as $attribute) {
            if ($urlAdditional = (array)$this->getRequest()->getParam('additional')) {
                if (array_key_exists($attribute['attribute_code'], $urlAdditional)) {
                    $layeredAttributes[$attribute['attribute_code']] = $urlAdditional[$attribute['attribute_code']];
                }
            }
        }
        return $layeredAttributes;
    }
}
