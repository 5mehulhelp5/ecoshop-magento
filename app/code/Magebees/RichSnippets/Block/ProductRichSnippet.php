<?php

namespace Magebees\RichSnippets\Block;

use Magento\Framework\View\Element\Template;

class ProductRichSnippet extends Template
{
    private $_product;
    private $_storeName;
    private $_currencyCode;

    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function setStoreName($storeName)
    {
        $this->_storeName = $storeName;
        return $this;
    }

    public function getStoreName()
    {
        return $this->_storeName;
    }

    public function setCurrencyCode($currencyCode)
    {
        $this->_currencyCode = $currencyCode;
        return $this;
    }

    public function getCurrencyCode()
    {
        return $this->_currencyCode;
    }

    public function getRichSnippetData()
    {
        $product = $this->getProduct();

        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'description' => $product->getShortDescription(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->getPrice(),
                'priceCurrency' => $this->getCurrencyCode(),
                'availability' => $product->isAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $this->getStoreName(),
                ],
            ],
        ];
    }
}
