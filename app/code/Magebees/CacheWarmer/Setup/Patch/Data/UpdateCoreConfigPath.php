<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Setup\Patch\Data;

use Magebees\CacheWarmer\Model\Serializer;
use Magebees\CacheWarmer\Model\Config;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollection;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateCoreConfigPath implements DataPatchInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ConfigCollection
     */
    private $configCollection;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ConfigInterface $config,
        ConfigCollection $configCollection,
        Serializer $serializer
    ) {
        $this->config = $config;
        $this->configCollection = $configCollection;
        $this->serializer = $serializer;
    }

    public function apply()
    {
        //transfer core config data from previous version, for version < 2.0.0
        $oldConfig = $this->configCollection
            ->create()
            ->addFieldToFilter('path', ['like'=>'%magebees_cachewarmer/crawler%'])
            ->getData();

        foreach ($oldConfig as $item => $data) {
            $this->processField($data);
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * Process one field for config data transfer
     *
     * @param array $data
     * @return void
     */
    private function processField(array $data): void
    {
        $field = ltrim(substr($data['path'], strripos($data['path'], '/')), '/');

        switch ($field) {
            case 'log_size':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::LOG_SIZE,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'delay':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::DELAY,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'process_cron':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::PROCESS_CRON,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'batch_size':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::BATCH_SIZE,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'max_queue_size':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::MAX_QUEUE_SIZE,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'generate_cron':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::GENERATE_CRON,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'page_types':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::PAGE_TYPES,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'file_path':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::FILE_PATH,
                    $data['value'],
                    $data['scope'],
                    $data['scope_id']
                );
                $this->config->deleteConfig(
                    $data['path'],
                    $data['scope'],
                    $data['scope_id']
                );
                break;
            case 'use_visit_params':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::USE_VISIT_PARAMS,
                    $data['value'],
                    $data['scope'],
                    $data['scope_id']
                );
                $this->config->deleteConfig(
                    $data['path'],
                    $data['scope'],
                    $data['scope_id']
                );
                break;
            case 'sitemap_path':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::SITEMAP_PATH,
                    $data['value'],
                    $data['scope'],
                    $data['scope_id']
                );
                $this->config->deleteConfig(
                    $data['path'],
                    $data['scope'],
                    $data['scope_id']
                );
                break;
            case 'source':
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::GENERATION_SOURCE,
                    $data['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
            case 'ignore_list':
                $result = $this->processIgnoreList($data['value']);
                $this->config->saveConfig(
                    Config::PATH_PREFIX . Config::EXCLUDE_PAGES,
                    $result,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $this->config->deleteConfig(
                    $data['path'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                break;
        }
    }

    /**
     * @param string $oldValue
     * @return bool|string
     */
    private function processIgnoreList(string $oldValue)
    {
        $result = [];
        $iterator = 0;
        $data = preg_split('/\n|\r\n?/', $oldValue);

        foreach ($data as $item) {
            if ($item == "") {
                continue;
            }
            $result['old_url_' . $iterator] = ['expression' => $item];
        }

        return $this->serializer->serialize($result);
    }
}
