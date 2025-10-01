<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source;

use Magebees\CacheWarmer\Model\Config\Source\QuerySource;
use Magento\Framework\ObjectManagerInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $type
     *
     * @return SourceInterface
     */
    public function create($type)
    {
        switch ($type) {
            case QuerySource::SOURCE_TEXT_FILE:
                $className = 'File';
                break;
            case QuerySource::SOURCE_SITE_MAP:
                $className = 'Sitemap';
                break;
            case QuerySource::SOURCE_ACTIVITY:
                $className = 'Activity';
                break;
            default:
                $className = 'All';
        }

        return $this->objectManager->create('\Magebees\CacheWarmer\Model\Source\\' . $className);
    }
}
