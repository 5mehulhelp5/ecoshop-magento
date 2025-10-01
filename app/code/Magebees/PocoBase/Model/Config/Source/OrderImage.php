<?php
namespace Magebees\PocoBase\Model\Config\Source;

class OrderImage implements \Magento\Framework\Option\ArrayInterface
{

    const LATEST = 'latest';
    const RANDOM = 'random';

    public function toOptionArray() {
        return [
            self::LATEST => __('Latest'),
            self::RANDOM => __('Random'),
        ];
    }

}
