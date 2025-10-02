<?php
/**
 * Console command to download product images from HTML files
 */
declare(strict_types=1);

namespace Ecoshop\ProductImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Framework\App\State;

class DownloadImages extends Command
{
    private const HTML_DIR = 'html-dir';
    private const LIMIT = 'limit';
    private const OVERWRITE = 'overwrite';

    private DirectoryList $directoryList;
    private ProductRepositoryInterface $productRepository;
    private CollectionFactory $productCollectionFactory;
    private State $state;

    public function __construct(
        DirectoryList $directoryList,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $productCollectionFactory,
        State $state,
        ?string $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->state = $state;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('ecoshop:import:download-images')
            ->setDescription('Download product images from HTML files')
            ->addArgument(
                self::HTML_DIR,
                InputArgument::OPTIONAL,
                'Directory with HTML files',
                'var/import/html'
            )
            ->addOption(
                self::LIMIT,
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit number of products to process',
                null
            )
            ->addOption(
                self::OVERWRITE,
                'o',
                InputOption::VALUE_NONE,
                'Overwrite existing images'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            // Area code already set
        }

        $htmlDir = $input->getArgument(self::HTML_DIR);
        $limit = $input->getOption(self::LIMIT);
        $overwrite = $input->getOption(self::OVERWRITE);

        $rootPath = $this->directoryList->getRoot();
        $fullHtmlPath = $rootPath . '/' . $htmlDir;
        $importPath = $this->directoryList->getPath('media') . '/import';

        if (!is_dir($fullHtmlPath)) {
            $output->writeln("<error>HTML directory not found: {$fullHtmlPath}</error>");
            return Command::FAILURE;
        }

        // Create import directory
        if (!is_dir($importPath)) {
            mkdir($importPath, 0777, true);
        }

        $output->writeln("<info>Scanning directory: {$fullHtmlPath}</info>");
        if ($overwrite) {
            $output->writeln("<comment>Overwrite mode enabled</comment>");
        }

        $htmlFiles = glob($fullHtmlPath . '/*.html');

        if (empty($htmlFiles)) {
            $output->writeln("<error>No HTML files found in directory</error>");
            return Command::FAILURE;
        }

        $output->writeln("<info>Found " . count($htmlFiles) . " HTML files</info>");

        if ($limit !== null) {
            $htmlFiles = array_slice($htmlFiles, 0, (int)$limit);
            $output->writeln("<info>Processing {$limit} files (limit applied)</info>");
        }

        $downloaded = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($htmlFiles as $htmlFile) {
            $filename = basename($htmlFile, '.html');
            $output->writeln('');
            $output->writeln("<info>Processing: {$filename}</info>");

            try {
                $html = file_get_contents($htmlFile);
                $productData = $this->parseProductData($html, $output);

                if (empty($productData['sku']) && empty($productData['name'])) {
                    $output->writeln("<comment>Could not parse SKU or name from {$filename}</comment>");
                    $skipped++;
                    continue;
                }

                if (empty($productData['images'])) {
                    $output->writeln("<comment>No images found in HTML</comment>");
                    $skipped++;
                    continue;
                }

                $output->writeln("  SKU: " . ($productData['sku'] ?? 'N/A'));
                $output->writeln("  Name: " . ($productData['name'] ?? 'N/A'));
                $output->writeln("  Images: " . count($productData['images']));

                // Find product
                $product = $this->findProduct($productData, $output);

                if (!$product) {
                    $output->writeln("<error>Product not found</error>");
                    $failed++;
                    continue;
                }

                // Download and assign images
                $result = $this->processImages($product, $productData['images'], $importPath, $overwrite, $output);
                $downloaded += $result['downloaded'];
                $skipped += $result['skipped'];
                $failed += $result['failed'];

            } catch (\Exception $e) {
                $output->writeln("<error>Error processing {$filename}: {$e->getMessage()}</error>");
                $failed++;
            }
        }

        $output->writeln('');
        $output->writeln("<info>Download complete!</info>");
        $output->writeln("<info>Downloaded: {$downloaded}</info>");
        $output->writeln("<comment>Skipped: {$skipped}</comment>");
        $output->writeln("<error>Failed: {$failed}</error>");

        return Command::SUCCESS;
    }

    private function parseProductData(string $html, OutputInterface $output): array
    {
        $data = [];

        // Extract product JSON from JavaScript
        if (preg_match('/var\s+product\s*=\s*(\{.*?\});/s', $html, $matches)) {
            $productJson = $matches[1];
            $productData = json_decode($productJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try advanced extraction
                $startPos = strpos($html, 'var product = ');
                if ($startPos !== false) {
                    $startPos += strlen('var product = ');
                    $productJson = $this->extractBalancedBraces($html, $startPos);
                    if ($productJson) {
                        $productData = json_decode($productJson, true);
                    }
                }
            }
        } else {
            $startPos = strpos($html, 'var product = ');
            if ($startPos !== false) {
                $startPos += strlen('var product = ');
                $productJson = $this->extractBalancedBraces($html, $startPos);
                if ($productJson) {
                    $productData = json_decode($productJson, true);
                }
            }
        }

        if (isset($productData) && is_array($productData)) {
            // Extract name
            $data['name'] = $productData['title'] ?? '';

            // Extract SKU
            $sku = $productData['sku'] ?? '';
            $data['sku'] = str_replace('_', '', $sku);

            // Extract images from gallery
            $data['images'] = [];
            if (!empty($productData['gallery']) && is_array($productData['gallery'])) {
                foreach ($productData['gallery'] as $galleryItem) {
                    if (!empty($galleryItem['img'])) {
                        $data['images'][] = [
                            'url' => $galleryItem['img'],
                            'alt' => $galleryItem['alt'] ?? ''
                        ];
                    }
                }
            }
        }

        return $data;
    }

    private function findProduct(array $productData, OutputInterface $output)
    {
        $sku = $productData['sku'] ?? '';
        $name = $productData['name'] ?? '';

        $product = null;

        // Try to find by SKU first
        if (!empty($sku)) {
            try {
                $product = $this->productRepository->get($sku, false, Store::DEFAULT_STORE_ID);
                $output->writeln("<comment>Found by SKU: {$sku}</comment>");
                return $product;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $output->writeln("<comment>Product with SKU {$sku} not found, trying by name...</comment>");
            }
        }

        // Fallback: search by exact name
        if (!empty($name)) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToFilter('name', $name);
            $collection->setPageSize(1);
            $collection->setStoreId(Store::DEFAULT_STORE_ID);

            if ($collection->getSize() > 0) {
                $product = $collection->getFirstItem();
                $output->writeln("<comment>Found by name: {$name}</comment>");
                return $product;
            }
        }

        return null;
    }

    private function processImages($product, array $images, string $importPath, bool $overwrite, OutputInterface $output): array
    {
        $stats = ['downloaded' => 0, 'skipped' => 0, 'failed' => 0];

        $addedImages = [];
        foreach ($images as $imageData) {
            $imageUrl = $imageData['url'] ?? '';

            if (empty($imageUrl)) {
                continue;
            }

            try {
                $output->writeln("<comment>  Downloading image: {$imageUrl}</comment>");

                // Download image
                $imageContent = $this->downloadImage($imageUrl);

                if ($imageContent === false) {
                    $output->writeln("<error>  Failed to download image: {$imageUrl}</error>");
                    $stats['failed']++;
                    continue;
                }

                // Generate filename from URL
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                if (empty($filename)) {
                    $filename = uniqid('img_') . '.jpg';
                }

                // Save to temp location
                $tmpPath = $importPath . '/' . $filename;
                file_put_contents($tmpPath, $imageContent);

                $mediaAttributes = null;
                if (count($addedImages) === 0) {
                    $mediaAttributes = ['image', 'small_image', 'thumbnail'];
                }

                // Add image to product gallery
                $imagePath = $product->addImageToMediaGallery($tmpPath, $mediaAttributes, true, false);
                $addedImages[] = $imagePath;

                $output->writeln("<info>  Image added to gallery: {$filename}</info>");
                $stats['downloaded']++;

            } catch (\Exception $e) {
                $output->writeln("<error>  Error importing image {$imageUrl}: {$e->getMessage()}</error>");
                $stats['failed']++;
            }
        }

        // Save product with images
        if (!empty($addedImages)) {
            try {
                $product = $this->productRepository->save($product);
                $output->writeln("<info>  Product images saved</info>");
            } catch (\Exception $e) {
                $output->writeln("<error>  Failed to save product: {$e->getMessage()}</error>");
            }
        }

        return $stats;
    }

    private function downloadImage(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Magento/2.4)');

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        return $content;
    }

    private function extractBalancedBraces(string $html, int $startPos): ?string
    {
        $length = strlen($html);
        $depth = 0;
        $inString = false;
        $stringChar = '';
        $escaped = false;
        $result = '';

        for ($i = $startPos; $i < $length; $i++) {
            $char = $html[$i];
            $result .= $char;

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if (($char === '"' || $char === "'") && !$inString) {
                $inString = true;
                $stringChar = $char;
                continue;
            }

            if ($char === $stringChar && $inString) {
                $inString = false;
                $stringChar = '';
                continue;
            }

            if ($inString) {
                continue;
            }

            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return $result;
                }
            }

            if ($char === ';' && $depth === 0) {
                return rtrim($result, ';');
            }
        }

        return null;
    }
}