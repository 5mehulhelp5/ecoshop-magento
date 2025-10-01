<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Config\Source;

use Magebees\CacheWarmer\Helper\Http as HttpHelper;

class ResponseCode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        HttpHelper $httpHelper
    ) {
        $this->httpHelper = $httpHelper;
    }

    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Already cached'),
                'value' => HttpHelper::STATUS_ALREADY_CACHED
            ]
        ];

        $codes = $this->httpHelper->getStatusCodes();

        foreach ($codes as $code => $description) {
            if ($code == HttpHelper::STATUS_ALREADY_CACHED) {
                continue;
            }

            $options []= [
                'label' => "$code $description",
                'value' => $code
            ];
        }

        return $options;
    }
}
