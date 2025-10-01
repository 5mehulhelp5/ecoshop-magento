<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Plugin\Setup\Model\FixtureGenerator\EntityGeneratorFactory;

use Magento\Setup\Model\FixtureGenerator\EntityGenerator;
use Magento\Setup\Model\FixtureGenerator\EntityGeneratorFactory;

class UpdateCustomTableMapPlugin
{
    /**
     * Inject magebees_cachewarmer_flushes_log table data to FixtureGenerator\EntityGeneratorFactory arguments.
     *
     * @param EntityGeneratorFactory $subject
     * @param array $data
     * @return array
     */
    public function beforeCreate(
        EntityGeneratorFactory $subject,
        array $data
    ): array {
        $data['customTableMap']['magebees_cachewarmer_flushes_log'] = [
            'entity_id_field' => EntityGenerator::SKIP_ENTITY_ID_BINDING
        ];

        return [$data];
    }
}
