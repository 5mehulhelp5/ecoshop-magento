<?php
/**
 * Product Import Module Registration
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Ecoshop_ProductImport',
    __DIR__
);