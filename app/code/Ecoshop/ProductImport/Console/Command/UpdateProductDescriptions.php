<?php
/**
 * Console command to update product descriptions from HTML files
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
use Magento\Framework\App\State;

class UpdateProductDescriptions extends Command
{
    private const HTML_DIR = 'html-dir';
    private const DRY_RUN = 'dry-run';
    private const LIMIT = 'limit';

    private DirectoryList $directoryList;
    private ProductRepositoryInterface $productRepository;
    private State $state;

    public function __construct(
        DirectoryList $directoryList,
        ProductRepositoryInterface $productRepository,
        State $state,
        ?string $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->productRepository = $productRepository;
        $this->state = $state;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('ecoshop:import:update-descriptions')
            ->setDescription('Update product descriptions from HTML files')
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
                'Dry run - parse but do not update'
            )
            ->addOption(
                self::LIMIT,
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit number of products to update',
                null
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
            $output->writeln("<comment>DRY RUN MODE - No products will be updated</comment>");
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

        $updated = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($htmlFiles as $htmlFile) {
            $filename = basename($htmlFile, '.html');
            $output->writeln('');
            $output->writeln("<info>Processing: {$filename}</info>");

            try {
                $html = file_get_contents($htmlFile);
                $productData = $this->parseProductData($html, $output);

                if (empty($productData['sku']) || empty($productData['description'])) {
                    $output->writeln("<comment>Could not parse SKU or description from {$filename}</comment>");
                    $skipped++;
                    continue;
                }

                $output->writeln("  SKU: " . $productData['sku']);
                $output->writeln("  Description length: " . strlen($productData['description']));

                if (!$dryRun) {
                    $this->updateProductDescription($productData['sku'], $productData['description'], $output);
                    $updated++;
                } else {
                    $output->writeln("<comment>[DRY RUN] Would update product</comment>");
                }

            } catch (\Exception $e) {
                $output->writeln("<error>Error processing {$filename}: {$e->getMessage()}</error>");
                $failed++;
            }
        }

        $output->writeln('');
        $output->writeln("<info>Update complete!</info>");
        $output->writeln("<info>Updated: {$updated}</info>");
        $output->writeln("<error>Failed: {$failed}</error>");
        $output->writeln("<comment>Skipped: {$skipped}</comment>");

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
            // Extract description
            if (!empty($productData['text'])) {
                $data['description'] = html_entity_decode($productData['text']);
            }

            // Extract SKU
            $sku = $productData['sku'] ?? '';
            $data['sku'] = str_replace('_', '', $sku);
        }

        return $data;
    }

    private function updateProductDescription(string $sku, string $description, OutputInterface $output): void
    {
        try {
            $product = $this->productRepository->get($sku);
            $product->setDescription($description);
            $this->productRepository->save($product);
            $output->writeln("<info>Updated product: {$product->getName()} (SKU: {$sku})</info>");
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $output->writeln("<error>Product with SKU {$sku} not found</error>");
            throw $e;
        }
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