<?php
/**
 * Console command to parse HTML and import products
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
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Filesystem\DirectoryList as FilesystemDirectoryList;
use Magento\Catalog\Model\Product\Gallery\Processor as GalleryProcessor;
use Magento\Framework\Filesystem\Io\File;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;

class ImportProducts extends Command
{
    private const HTML_DIR = 'html-dir';
    private const DRY_RUN = 'dry-run';
    private const LIMIT = 'limit';

    private DirectoryList $directoryList;
    private ProductRepositoryInterface $productRepository;
    private ProductInterfaceFactory $productFactory;
    private State $state;
    private StoreManagerInterface $storeManager;
    private GalleryProcessor $galleryProcessor;
    private File $file;
    private AttributeRepositoryInterface $attributeRepository;

    public function __construct(
        DirectoryList $directoryList,
        ProductRepositoryInterface $productRepository,
        ProductInterfaceFactory $productFactory,
        State $state,
        StoreManagerInterface $storeManager,
        GalleryProcessor $galleryProcessor,
        File $file,
        AttributeRepositoryInterface $attributeRepository,
        ?string $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->galleryProcessor = $galleryProcessor;
        $this->file = $file;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('ecoshop:import:products')
            ->setDescription('Parse HTML files and import products')
            ->addArgument(
                self::HTML_DIR,
                InputArgument::OPTIONAL,
                'Directory with HTML files',
                'var/import/html'
            )
            ->addOption(
                self::DRY_RUN,
                'd',
                InputOption::VALUE_NONE,
                'Dry run - parse but do not import'
            )
            ->addOption(
                self::LIMIT,
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit number of products to import',
                null
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Set area code for Magento operations
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            // Area code already set
        }

        $htmlDir = $input->getArgument(self::HTML_DIR);
        $dryRun = $input->getOption(self::DRY_RUN);
        $limit = $input->getOption(self::LIMIT);

        $rootPath = $this->directoryList->getRoot();
        $fullHtmlPath = $rootPath . '/' . $htmlDir;

        if (!is_dir($fullHtmlPath)) {
            $output->writeln("<error>HTML directory not found: {$fullHtmlPath}</error>");
            return Command::FAILURE;
        }

        $output->writeln("<info>Scanning directory: {$fullHtmlPath}</info>");
        if ($dryRun) {
            $output->writeln("<comment>DRY RUN MODE - No products will be imported</comment>");
        }

        // Get all HTML files
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

        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $products = [];

        foreach ($htmlFiles as $htmlFile) {
            $filename = basename($htmlFile, '.html');
            $output->writeln('');
            $output->writeln("<info>Processing: {$filename}</info>");

            try {
                // Load HTML
                $html = file_get_contents($htmlFile);

                // Load metadata JSON if exists
                $jsonFile = dirname($htmlFile) . '/' . $filename . '.json';
                $metadata = [];
                if (file_exists($jsonFile)) {
                    $metadata = json_decode(file_get_contents($jsonFile), true) ?? [];
                }

                // Parse product data from HTML
                $productData = $this->parseProductData($html, $metadata, $output);
                $products[] = $productData;

                if (empty($productData)) {
                    $output->writeln("<comment>Could not parse product data from {$filename}</comment>");
                    $skipped++;
                    continue;
                }

                // Display parsed data
                $output->writeln("<info>Parsed data:</info>");
                $output->writeln("  Name: " . ($productData['name'] ?? 'N/A'));
                $output->writeln("  SKU: " . ($productData['sku'] ?? 'N/A'));
                $output->writeln("  Price: " . ($productData['price'] ?? 'N/A'));
                $output->writeln("  Description length: " . strlen($productData['description'] ?? ''));

                if (!$dryRun) {
                    // Import product
                    $this->importProduct($productData, $output);
                    $imported++;
                } else {
                    $output->writeln("<comment>[DRY RUN] Would import product</comment>");
                }

            } catch (\Exception $e) {
                $output->writeln("<error>Error processing {$filename}: {$e->getMessage()}</error>");
                $output->writeln("<error>Stack trace: {$e->getTraceAsString()}</error>");
                $failed++;
            }
        }

        $output->writeln('');
        $output->writeln("<info>Import complete!</info>");
        $output->writeln("<info>Imported: {$imported}</info>");
        $output->writeln("<error>Failed: {$failed}</error>");
        $output->writeln("<comment>Skipped: {$skipped}</comment>");

        return Command::SUCCESS;
    }

    private function parseProductData(string $html, string $metadata, OutputInterface $output): array
    {
        $data = [];

        // First, try to extract product JSON from JavaScript
        // Find "var product = " and extract everything until the next semicolon at the same level
        if (preg_match('/var\s+product\s*=\s*(\{.*?\});/s', $html, $matches)) {
            $output->writeln("<info>Found product JSON in JavaScript (simple match)</info>");
            $productJson = $matches[1];

            $output->writeln("<comment>JSON length: " . strlen($productJson) . " chars</comment>");
            $output->writeln("<comment>First 200 chars: " . substr($productJson, 0, 200) . "...</comment>");

            $productData = json_decode($productJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->writeln("<error>JSON decode error: " . json_last_error_msg() . "</error>");
                $productData = null;
            } else {
                $output->writeln("<info>JSON decoded successfully</info>");
            }
        } else {
            // Try more complex extraction - find balanced braces
            $output->writeln("<comment>Trying advanced JSON extraction...</comment>");

            $startPos = strpos($html, 'var product = ');
            if ($startPos !== false) {
                $startPos += strlen('var product = ');
                $productJson = $this->extractBalancedBraces($html, $startPos);

                if ($productJson) {
                    $output->writeln("<info>Found product JSON with balanced braces extraction</info>");
                    $productData = json_decode($productJson, true);
                } else {
                    $productData = null;
                }
            } else {
                $productData = null;
            }
        }

        if ($productData && is_array($productData)) {

            if ($productData && is_array($productData)) {
                $output->writeln("<info>Successfully parsed product JSON</info>");

                // Extract main product data
                $data['name'] = $productData['title'] ?? '';
                $data['brand'] = $productData['brand'] ?? '';
                $data['short_description'] = $productData['descr'] ?? '';
                $data['price'] = $productData['price'] ?? '';

                // Extract full description (HTML content)
                if (!empty($productData['text'])) {
                    $data['description'] = strip_tags(html_entity_decode($productData['text']));
                }

                // Extract images from gallery
                if (!empty($productData['gallery']) && is_array($productData['gallery'])) {
                    $data['images'] = [];
                    foreach ($productData['gallery'] as $galleryItem) {
                        if (!empty($galleryItem['img'])) {
                            $data['images'][] = [
                                'url' => $galleryItem['img'],
                                'alt' => $galleryItem['alt'] ?? ''
                            ];
                        }
                    }
                }

                // Extract characteristics
                if (!empty($productData['characteristics']) && is_array($productData['characteristics'])) {
                    $data['characteristics'] = $productData['characteristics'];
                }

                // Extract SKU from first edition or generate
                $sku = $productData['sku'] ?? '';
                // Remove underscores from SKU
                $data['sku'] = str_replace('_', '', $sku);

                // Store full product data for debugging
                $data['raw_product_json'] = $productData;

                // Extract category slug from URL path
                if (!empty($productData['url'])) {
                    $urlPath = parse_url($productData['url'], PHP_URL_PATH);
                    $pathSegments = array_filter(explode('/', $urlPath));
                    $data['category'] = reset($pathSegments) ?: '';
                }
            }
        }

        // Fallback: try to parse from HTML if JSON not found
        if (empty($data['name'])) {
            $output->writeln("<comment>Product JSON not found, trying HTML parsing...</comment>");

            // Create DOMDocument
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            // Try to extract product name
            $nameNodes = $xpath->query('//h1[@class="product-title"] | //h1[@itemprop="name"] | //h1[contains(@class, "product")]');
            if ($nameNodes->length > 0) {
                $data['name'] = trim($nameNodes->item(0)->textContent);
            }

            // Try to extract SKU
            $skuNodes = $xpath->query('//*[@itemprop="sku"] | //*[contains(@class, "sku")]');
            if ($skuNodes->length > 0) {
                $data['sku'] = trim($skuNodes->item(0)->textContent);
            }

            // Try to extract price
            $priceNodes = $xpath->query('//*[@itemprop="price"] | //*[contains(@class, "price")]');
            if ($priceNodes->length > 0) {
                $priceText = trim($priceNodes->item(0)->textContent);
                // Extract numeric value
                $data['price'] = preg_replace('/[^0-9.,]/', '', $priceText);
                $data['price'] = str_replace(',', '.', $data['price']);
            }
        }

        if (empty($data['sku'])) {
            // Generate SKU from name or use filename
            $data['sku'] = $this->generateSku($data['name'] ?? $metadata ?? uniqid('PROD_'));
        }

        $output->writeln("<comment>DEBUG: Parsed data structure</comment>");
        $output->writeln(print_r($data, true));

        return $data;
    }

    private function importProduct(array $data, OutputInterface $output): void
    {
        if (empty($data['name']) || empty($data['sku'])) {
            throw new \Exception('Product name and SKU are required');
        }

        // Check if product already exists
        try {
            $existingProduct = $this->productRepository->get($data['sku']);
            $output->writeln("<comment>Product with SKU {$data['sku']} already exists. Updating...</comment>");
            $product = $existingProduct;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Create new product
            $product = $this->productFactory->create();
        }

        // Set basic attributes
        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setTypeId(Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4); // Default attribute set
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $product->setPrice($data['price'] ?? 0);
        $product->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_in_stock' => 1
        ]);

        if (!empty($data['description'])) {
            $product->setDescription($data['description']);
        }

        if (!empty($data['short_description'])) {
            $product->setShortDescription($data['short_description']);
        }

        // Set default country
        $product->setData('country_of_manufacture', 'TR');

        // Map and set characteristics as product attributes
        if (!empty($data['characteristics']) && is_array($data['characteristics'])) {
            $mappedAttributes = $this->mapCharacteristicsToAttributes($data['characteristics']);
            foreach ($mappedAttributes as $attributeCode => $value) {
                try {
                    if ($attributeCode === 'quantity_pcs'
                        && (str_contains($value, 'стиков')
                            || str_contains($value, 'таблеток')
                            || str_contains($value, 'капсул')
                            || str_contains($value, 'пакетиков')
                            || str_contains($value, 'тестеров')
                        )
                    ) {
                        switch ($value) {
                            case str_contains($value, 'стиков'):
                                $product->setData('quantity_pcs_type', 'стиков');
                            case str_contains($value, 'таблеток'):
                                $product->setData('quantity_pcs_type', 'таблеток');
                            case str_contains($value, 'капсул'):
                                $product->setData('quantity_pcs_type', 'капсул');
                            case str_contains($value, 'пакетиков'):
                                $product->setData('quantity_pcs_type', 'пакетиков');
                            case str_contains($value, 'тестеров'):
                                $product->setData('quantity_pcs_type', 'тестеров');
                        }
                    }
                    if (in_array($attributeCode, ['quantity_pcs', 'volume'])) {
                        $value = (int)$value;
                    }
                    if (in_array($attributeCode, ['weight'])) {
                        $value = (float)$value;
                        // Convert weight from grams to kilograms
                        if ($attributeCode === 'weight') {
                            $value = $value / 1000;
                        }
                    }
                    if (in_array($attributeCode, ['supplement_form','age_group','hair_type','skin_type','material','purpose'])) {
                        $value = $this->getAttributeOptionId($attributeCode, $value);
                    }
                    $product->setData($attributeCode, $value);
                    $output->writeln("<comment>  Set {$attributeCode}: {$value}</comment>");
                } catch (\Exception $e) {
                    $output->writeln("<error>  Failed to set {$attributeCode}: {$e->getMessage()}</error>");
                }
            }
        }

        // Assign category
        if (!empty($data['category'])) {
            $categoryId = $this->getCategoryIdBySlug($data['category']);
            if ($categoryId) {
                $product->setCategoryIds([$categoryId]);
                $output->writeln("<comment>  Assigned to category ID: {$categoryId}</comment>");
            } else {
                $output->writeln("<comment>  Category not found for slug: {$data['category']}</comment>");
            }
        }

        // Save product first to get ID
        $product = $this->productRepository->save($product);
        $output->writeln("<info>Product saved: {$data['name']} (SKU: {$data['sku']})</info>");

        // Import images
        if (!empty($data['images']) && is_array($data['images'])) {
            $this->importProductImages($product, $data['images'], $output);
        }
    }

    private function generateSku(string $name): string
    {
        // Remove special characters and convert to uppercase
        $sku = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $name));
        // Limit length
        $sku = substr($sku, 0, 50);
        // Add random suffix to ensure uniqueness
        return $sku . '_' . substr(uniqid(), -6);
    }

    /**
     * Map Russian characteristic titles to attribute codes
     */
    private function mapCharacteristicsToAttributes(array $characteristics): array
    {
        $mapping = [
            'Количество, шт' => 'quantity_pcs',
            'Капсулы №1' => 'quantity_pcs',
            'Капсулы №2' => 'quantity_pcs',
            'Вес' => 'weight',
            'Объём' => 'volume',
            'Объём дозатора' => 'volume',
            'Объём тары' => 'volume',
            'Назначение' => 'purpose',
            'Материал' => 'material',
            'Тип кожи' => 'skin_type',
            'Типы волос' => 'hair_type',
            'Возраст' => 'age_group',
            'Форма выпускаемого бада' => 'supplement_form',
            'Объём сиропа' => 'volume',
            'Объём пробника' => 'volume',
            'Объём парфюма' => 'volume'
        ];

        $attributes = [];
        foreach ($characteristics as $char) {
            $title = $char['title'] ?? '';
            $value = $char['value'] ?? '';

            if (isset($mapping[$title]) && !empty($value)) {
                $attributes[$mapping[$title]] = $value;
            }
        }
        return $attributes;
    }

    /**
     * Get category ID by URL slug
     */
    private function getCategoryIdBySlug(string $slug): ?int
    {
        $categoryMapping = [
            'bady-and-vitaminy' => 3,
            'lichnaya-gigiena' => 11,
            'clean-home' => 12,
            'care-of-face-and-body' => 9,
            'cosmetica-ersag' => 13,
            'parfum' => 14,
            'ukhod-za-volosami' => 10,
            'detskay-liniya' => 15,
            'tekstil-ersag' => 16
        ];

        return $categoryMapping[$slug] ?? null;
    }

    /**
     * Get option ID for select attribute by text value
     */
    private function getAttributeOptionId(string $attributeCode, string $optionText): ?string
    {
        try {
            $attribute = $this->attributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
            $options = $attribute->getOptions();

            foreach ($options as $option) {
                if ($option->getLabel() === $optionText) {
                    return $option->getValue();
                }
            }
        } catch (\Exception $e) {
            // Attribute not found or error, return text value
        }

        // Return the text value if option ID not found - Magento may handle it
        return $optionText;
    }

    /**
     * Import product images from URLs
     */
    private function importProductImages($product, array $images, OutputInterface $output): void
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $mediaPath = $this->directoryList->getPath('media');
        $importPath = $mediaPath . '/import';

        // Create import directory if it doesn't exist
        if (!is_dir($importPath)) {
            mkdir($importPath, 0755, true);
        }

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
                // Add image to product gallery without roles first
                $imagePath = $product->addImageToMediaGallery($tmpPath, $mediaAttributes, true, false);
                $addedImages[] = $imagePath;

                $output->writeln("<info>  Image added to gallery: {$filename}</info>");

            } catch (\Exception $e) {
                $output->writeln("<error>  Error importing image {$imageUrl}: {$e->getMessage()}</error>");
            }
        }

        // Save product with images first
        try {
            $product = $this->productRepository->save($product);
            $output->writeln("<info>  Product images saved</info>");

        } catch (\Exception $e) {
            $output->writeln("<error>  Failed to save product images: {$e->getMessage()}</error>");
        }
    }

    /**
     * Download image from URL
     */
    private function downloadImage(string $url): string|false
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $content === false) {
            return false;
        }

        return $content;
    }

    /**
     * Extract balanced braces from HTML starting at position
     */
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

            // Handle escape sequences
            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            // Handle strings
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

            // Don't count braces inside strings
            if ($inString) {
                continue;
            }

            // Count braces
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;

                // Found matching closing brace
                if ($depth === 0) {
                    return $result;
                }
            }

            // Stop if we hit semicolon at depth 0
            if ($char === ';' && $depth === 0) {
                return rtrim($result, ';');
            }
        }

        return null;
    }
}
