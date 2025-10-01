<?php
namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Model\GetCustomerIp;
use Magebees\CacheWarmer\Model\Serializer;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\DataObject;
use Magento\PageCache\Model\Config as VarnishConfig;

class Config extends DataObject
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const PATH_PREFIX = 'magebees_cachewarmer/';

    public const IS_ENABLED = 'general/enabled';

    public const AUTO_UPDATE = 'general/auto_update';

    public const QUEUE_REGENERATE = 'general/queue_regenerate';

    public const QUEUE_REGENERATE_BACKGROUND = 'general/background_queue_regenerate';

    public const FLUSHES_LOG = 'general/enable_flushes_log';

    public const IGNORE_CLASSES = 'general/ignore_classes';

    public const FLUSHES_LOG_CLEANING = 'general/flushes_log_cleaning';

    public const FLUSHES_LOG_PERIOD = 'general/flushes_log_period';

    public const CUSTOMER_ACTIVITY = 'general/customer_activity';

    public const ACTIVITY_LOG_CLEANING = 'general/activity_log_cleaning';

    public const ACTIVITY_LOG_CLEANING_PERIOD = 'general/activity_log_cleaning_period';

    public const PERFORMANCE_REPORTS_CLEANING = 'general/performance_reports_cleaning';

    public const PERFORMANCE_REPORTS_PERIOD = 'general/performance_reports_period';

    public const GENERATION_SOURCE = 'source_and_priority/source';

    public const PAGE_TYPES = 'source_and_priority/page_types';

    public const FILE_PATH = 'source_and_priority/file_path';

    public const USE_VISIT_PARAMS = 'source_and_priority/use_visit_params';

    public const SITEMAP_PATH = 'source_and_priority/sitemap_path';

    public const MULTIPLE_CURL = 'performance_settings/multiple_curl';

    public const PROCESSES_NUMBER = 'performance_settings/processes_number';

    public const MAX_QUEUE_SIZE = 'performance_settings/max_queue_size';

    public const BATCH_SIZE = 'performance_settings/batch_size';

    public const DELAY = 'performance_settings/delay';

    public const LOG_SIZE = 'performance_settings/log_size';

    public const EXCLUDE_PAGES = 'combinations/ignore_list';

    public const PROCESS_MOBILE = 'combinations/process_mobile';

    public const MOBILE_AGENT = 'combinations/mobile_agent';

    public const USER_AGENTS = 'combinations/user_agents';

    public const HOLE_PUNCH  = 'hole_punch/hole_punch';

    public const HTTP_AUTHENTICATION = 'connection/http_auth';

    public const LOGIN = 'connection/login';

    public const PASSWORD = 'connection/password';

    public const SKIP_VERIFICATION = 'connection/skip_verification';

    public const PROCESS_CRON = 'performance_settings/process_cron';

    public const GENERATE_CRON = 'performance_settings/generate_cron';

    public const SHOW_STATUS = 'debug/show_status';

    public const CONTEXT_DEBUG = 'debug/context_debug';

    public const IPS = 'debug/ips';

    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigDataCollectionFactory
     */
    private $configCollection;

    /**
     * @var GetCustomerIp
     */
    private $getCustomerIp;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigDataCollectionFactory $configCollection,
        HttpRequest $request, // @deprecated
        Serializer $serializer,
        array $data = [],
        ?GetCustomerIp $getCustomerIp = null
    ) {
        parent::__construct($data);
        $this->scopeConfig = $scopeConfig;
        $this->configCollection = $configCollection;
        $this->serializer = $serializer;
        $this->getCustomerIp = $getCustomerIp ?? ObjectManager::getInstance()->get(GetCustomerIp::class);
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . $path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isSetFlag($path)
    {
        return $this->scopeConfig->isSetFlag(self::PATH_PREFIX . $path);
    }

    /**
     * @param string $enabledSetting
     * @param string $combinationsSetting
     *
     * @return array
     */
    protected function getCombinations($enabledSetting, $combinationsSetting)
    {
        if (!$this->isSetFlag('combinations/' . $enabledSetting)) {
            return [];
        }

        $values = $this->getValue('combinations/' . $combinationsSetting);

        return $this->split($values);
    }

    /**
     * Convert comma separated string to array
     *
     * @param $string
     *
     * @return array
     */
    protected function split($string)
    {
        $string = trim($string);

        if ($string == "") {
            return [];
        } else {
            return explode(',', $string);
        }
    }

    /**
     * Return all config items by path
     *
     * @param string $path
     *
     * @return array
     */
    public function getAllValuesByPath($path)
    {
        $configCollection = $this->configCollection->create();
        $configCollection->addFieldToFilter('path', ['eq' => $path]);

        return $configCollection->getData();
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->isSetFlag(self::IS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isAutoUpdate()
    {
        return $this->isSetFlag(self::AUTO_UPDATE);
    }

    /**
     * @return bool
     */
    public function isMultipleCurl()
    {
        return $this->isSetFlag(self::MULTIPLE_CURL);
    }

    /**
     * @return int
     */
    public function getProcessesNumber()
    {
        return (int)$this->getValue(self::PROCESSES_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getQueueAfterGenerate()
    {
        return $this->getValue(self::QUEUE_REGENERATE);
    }

    public function isRegenerateQueueInBackground(): bool
    {
        return $this->isSetFlag(self::QUEUE_REGENERATE_BACKGROUND);
    }

    /**
     * @return bool
     */
    public function isEnableFlushesLog()
    {
        return $this->isSetFlag(self::FLUSHES_LOG);
    }

    /**
     * @return bool
     */
    public function isNeedCleanFlushesLog(): bool
    {
        return $this->isSetFlag(self::FLUSHES_LOG_CLEANING);
    }

    /**
     * @return int
     */
    public function getFlushesLogPeriod(): int
    {
        return (int)$this->getValue(self::FLUSHES_LOG_PERIOD);
    }

    /**
     * @return bool
     */
    public function isLogCustomerActivity()
    {
        return $this->isSetFlag(self::CUSTOMER_ACTIVITY);
    }

    public function isNeedCleanLogCustomerActivity(): bool
    {
        return $this->isSetFlag(self::ACTIVITY_LOG_CLEANING);
    }

    public function getActivityLogCleaningPeriod(): ?string
    {
        return $this->getValue(self::ACTIVITY_LOG_CLEANING_PERIOD);
    }

    /**
     * @return bool
     */
    public function isNeedCleanPerformanceReports(): bool
    {
        return $this->isSetFlag(self::PERFORMANCE_REPORTS_CLEANING);
    }

    /**
     * @return int
     */
    public function getPerformanceReportsPeriod(): int
    {
        return (int)$this->getValue(self::PERFORMANCE_REPORTS_PERIOD);
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->getValue(self::GENERATION_SOURCE);
    }

    /**
     * @return string
     */
    public function getQueueLimit()
    {
        return $this->getValue(self::MAX_QUEUE_SIZE);
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return (int)$this->getValue(self::BATCH_SIZE);
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return (int)$this->getValue(self::DELAY);
    }

    /**
     * @return int
     */
    public function getLogSize()
    {
        return (int)$this->getValue(self::LOG_SIZE);
    }

    /**
     * @return array
     */
    public function getExcludePages()
    {
        return $this->serializer->unserialize($this->getValue(self::EXCLUDE_PAGES));
    }

    /**
     * @return array
     */
    public function getIgnoreClasses()
    {
        return $this->serializer->unserialize($this->getValue(self::IGNORE_CLASSES));
    }

    /**
     * @return array
     */
    public function getHolePunchBlocks()
    {
        $holePunchBlocks = $this->getValue(self::HOLE_PUNCH);

        if ($holePunchBlocks) {
            return (array)$this->serializer->unserialize($holePunchBlocks);
        }

        return [];
    }

    /**
     * @return bool
     */
    public function isProcessMobile()
    {
        return (bool)$this->getValue(self::PROCESS_MOBILE);
    }

    /**
     * @return string
     */
    public function getMobileAgent()
    {
        return $this->getValue(self::MOBILE_AGENT);
    }

    /**
     * @return string
     */
    public function getUserAgents()
    {
        return $this->getValue(self::USER_AGENTS);
    }

    /**
     * @return bool
     */
    public function isHttpAuth()
    {
        return $this->isSetFlag(self::HTTP_AUTHENTICATION);
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->getValue(self::LOGIN);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getValue(self::PASSWORD);
    }

    /**
     * @return bool
     */
    public function isSkipVerification()
    {
        return $this->isSetFlag(self::SKIP_VERIFICATION);
    }

    /**
     * @return array
     */
    public function getStores()
    {
        return $this->getCombinations('switch_stores', 'stores');
    }

    /**
     * @return array
     */
    public function getCurrencies()
    {
        return $this->getCombinations('switch_currencies', 'currencies');
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        return $this->getCombinations('switch_customer_groups', 'customer_groups');
    }

    /**
     * @return bool
     */
    public function isVarnishEnabled()
    {
        return $this->scopeConfig->getValue(VarnishConfig::XML_PAGECACHE_TYPE) == VarnishConfig::VARNISH;
    }

    /**
     * @return array
     */
    public function getDebugIps()
    {
        $ips = $this->getValue(self::IPS) ?? '';
        $ips = preg_split('/\s*,\s*/', trim($ips), -1, PREG_SPLIT_NO_EMPTY);

        return $ips;
    }

    /**
     * @return bool
     */
    public function canDisplayStatus()
    {
        if (!$this->isSetFlag(self::SHOW_STATUS)) {
            return false;
        }

        if ($allowedIps = $this->getDebugIps()) {
            $clientIp = $this->getCustomerIp->getCurrentIp();

            if (!in_array($clientIp, $allowedIps)) {
                return false;
            }
        }

        return true;
    }

    public function isDebugContext(): bool
    {
        return $this->isSetFlag(self::CONTEXT_DEBUG);
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function getPagesConfig()
    {
        $config = $this->getValue(self::PAGE_TYPES);

        return $this->serializer->unserialize($config);
    }

    public function isUseVisitParams(): bool
    {
        return $this->isSetFlag(self::USE_VISIT_PARAMS);
    }
}
