<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Simplexml\ConfigFactory as SimpleXmlConfigFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Simplexml\Element;
use Psr\Log\LoggerInterface;
use Laminas\Uri\Http as UriHandler;

class Sitemap implements SourceInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SimpleXmlConfigFactory
     */
    private $simpleXmlConfigFactory;

    /**
     * @var UriHandler
     */
    private $uriHandler;

    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger,
        Config $config,
        SimpleXmlConfigFactory $simpleXmlConfigFactory,
        UriHandler $uriHandler
    ) {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->config = $config;
        $this->simpleXmlConfigFactory = $simpleXmlConfigFactory;
        $this->uriHandler = $uriHandler;
    }

    /**
     * Return pages to crawl from Sitemap XML
     *
     * @param int    $queueLimit
     * @param string $eMessage
     *
     * @return array
     */
    public function getPages($queueLimit, $eMessage)
    {
        $result = [];
        $counter = 0;

        $allSitemaps = $this->config->getAllValuesByPath('magebees_cachewarmer/source_and_priority/sitemap_path');
        foreach ($allSitemaps as $item) {
            if ($counter == $queueLimit) {
                break;
            }

            $filePath = $item['value'];

            if (empty($filePath)) {
                continue;
            }

            $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);

            if (!$directoryRead->isExist($filePath)) {
                $this->logger->warning(
                    $eMessage . __('but the Sitemap XML file does not exist with specified path: %1', $filePath)
                );
                continue;
            }

            $xml = $this->loadXmlFromFile($directoryRead->getAbsolutePath($filePath));

            if (false === $xml) {
                $this->logger->warning(
                    $eMessage . __('but Magebees Crawler could not parse the Sitemap XML file: %1', $filePath)
                );
                continue;
            }

            $sitemapParts = [];
            if ('sitemapindex' == $xml->getName()) {
                foreach ($xml->sitemap as $sitemap) {
                    $sitemapPartPath = $this->uriHandler->parse(trim($sitemap->loc))->getPath();

                    if (false === $sitemapPartPath) {
                        $this->logger->warning(
                            $eMessage .
                            __(
                                'Magebees Crawler could not parse the following URL from the Sitemap XML file: %1',
                                trim($sitemap->loc)
                            )
                        );
                        continue;
                    }

                    if (!$directoryRead->isExist($sitemapPartPath)) {
                        $this->logger->warning(
                            $eMessage .
                            __('The following file from the Sitemap XML file does not exist: %1', $sitemapPartPath)
                        );
                    }

                    $sitemapPart = $this->loadXmlFromFile($directoryRead->getAbsolutePath($sitemapPartPath));

                    if (false === $sitemapPart) {
                        $this->logger->warning(
                            $eMessage .
                            __(
                                'Magebees Crawler could not parse the following file from the Sitemap XML file: %1',
                                $sitemapPartPath
                            )
                        );
                        continue;
                    }

                    $sitemapParts[] = $sitemapPart;
                }

                if (empty($sitemapParts)) {
                    $this->logger->warning(
                        $eMessage .
                        __('but Magebees Crawler could not extract any URL from the Sitemap XML file: %1', $filePath)
                    );
                    continue;
                }
            } else {
                $sitemapParts[] = $xml;
            }

            foreach ($sitemapParts as $sitemap) {
                if ($counter == $queueLimit) {
                    break;
                }

                foreach ($sitemap->url as $url) {
                    if ($counter == $queueLimit) {
                        break;
                    }

                    $result[] = [
                        //convert float 0.5 into percent value 50%
                        'rate' => isset($url->priority) ? round(trim($url->priority) * 100) : 0,
                        'url'  => trim($url->loc),
                        'store' => $item['scope_id']
                    ];

                    $counter++;
                }
            }
        }

        return $result;
    }

    protected function loadXmlFromFile(string $filePath): Element
    {
        $simplexmlConfig = $this->simpleXmlConfigFactory->create();
        $simplexmlConfig->loadFile($filePath);

        return $simplexmlConfig->getNode();
    }
}
