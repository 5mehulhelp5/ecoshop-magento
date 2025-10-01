<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Config\Source;

class Currency extends \Magento\Config\Model\Config\Source\Locale\Currency
{
    /**
     * @var \Magento\Directory\Model\Currency
     */
    private $currencyDirectory;

    public function __construct(
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Directory\Model\Currency $currencyDirectory
    ) {
        parent::__construct($localeLists);
        $this->currencyDirectory = $currencyDirectory;
    }

    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $allowed = $this->currencyDirectory->getConfigAllowCurrencies();

        $result = [];

        foreach ($options as $option) {
            if (in_array($option['value'], $allowed)) {
                $result []= $option;
            }
        }

        return $result;
    }
}
