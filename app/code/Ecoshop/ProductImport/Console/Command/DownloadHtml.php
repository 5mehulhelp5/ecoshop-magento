<?php
/**
 * Console command to download HTML from JSON URLs
 */
declare(strict_types=1);

namespace Ecoshop\ProductImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem\DirectoryList;

class DownloadHtml extends Command
{
    private const JSON_FILE = 'json-file';
    private const OUTPUT_DIR = 'output-dir';

    private DirectoryList $directoryList;

    public function __construct(
        DirectoryList $directoryList,
        ?string $name = null
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('ecoshop:download:html')
            ->setDescription('Download HTML files from JSON URLs')
            ->addArgument(
                self::JSON_FILE,
                InputArgument::REQUIRED,
                'Path to JSON file with URLs'
            )
            ->addOption(
                self::OUTPUT_DIR,
                'o',
                InputOption::VALUE_OPTIONAL,
                'Output directory for HTML files',
                'var/import/html'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jsonFile = $input->getArgument(self::JSON_FILE);
        $outputDir = $input->getOption(self::OUTPUT_DIR);

        $rootPath = $this->directoryList->getRoot();
        $fullOutputPath = $rootPath . '/' . $outputDir;

        // Create output directory if it doesn't exist
        if (!is_dir($fullOutputPath)) {
            mkdir($fullOutputPath, 0755, true);
            $output->writeln("<info>Created directory: {$fullOutputPath}</info>");
        }

        // Check if JSON file exists
        if (!file_exists($jsonFile)) {
            $output->writeln("<error>JSON file not found: {$jsonFile}</error>");
            return Command::FAILURE;
        }

        // Read and decode JSON
        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln("<error>Invalid JSON file: " . json_last_error_msg() . "</error>");
            return Command::FAILURE;
        }

        if (!is_array($data)) {
            $output->writeln("<error>JSON must contain an array</error>");
            return Command::FAILURE;
        }

        $output->writeln("<info>Starting HTML download...</info>");
        $downloaded = 0;
        $failed = 0;

        foreach ($data as $index => $item) {
            // Expect each item to have 'url' field
            $url = $item;

            if (!$url) {
                $output->writeln("<comment>Skipping item {$index}: no URL found</comment>");
                $failed++;
                continue;
            }

            try {
                $output->writeln("Downloading: {$url}");

                // Download HTML
                $html = $this->downloadHtml($url, $output);

                if ($html === false) {
                    $output->writeln("<error>Failed to download: {$url}</error>");
                    $failed++;
                    continue;
                }

                // Generate filename from URL or use index
                $filename = $this->generateFilename($url, $index);
                $filepath = $fullOutputPath . '/' . $filename . '.html';

                // Save HTML
                file_put_contents($filepath, $html);

                // Save metadata JSON alongside HTML
                $metaFilepath = $fullOutputPath . '/' . $filename . '.json';
                file_put_contents($metaFilepath, json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $output->writeln("<info>Saved: {$filename}.html</info>");
                $downloaded++;

                // Small delay to avoid overwhelming server
                usleep(5000000); // 0.5 seconds

            } catch (\Exception $e) {
                $output->writeln("<error>Error processing {$url}: {$e->getMessage()}</error>");
                $failed++;
            }
        }

        $output->writeln('');
        $output->writeln("<info>Download complete!</info>");
        $output->writeln("<info>Downloaded: {$downloaded}</info>");
        $output->writeln("<error>Failed: {$failed}</error>");
        $output->writeln("<info>Output directory: {$fullOutputPath}</info>");

        return Command::SUCCESS;
    }

    private function downloadHtml(string $url, OutputInterface $output): string|false
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1'
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno !== 0) {
            $output->writeln("<error>cURL error ({$errno}): {$error}</error>");
            return false;
        }

        if ($httpCode !== 200 && $httpCode !== 0) {
            $output->writeln("<error>HTTP code {$httpCode}</error>");
            return false;
        }

        if ($html === false) {
            $output->writeln("<error>Empty response</error>");
            return false;
        }

        return $html;
    }

    private function generateFilename(string $url, int $index): string
    {
        // Extract product slug or ID from URL
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';

        // Remove trailing slash and get last segment
        $segments = explode('/', trim($path, '/'));
        $lastSegment = end($segments);

        if ($lastSegment && $lastSegment !== '') {
            // Clean filename
            $filename = preg_replace('/[^a-z0-9_-]/i', '_', $lastSegment);
            return substr($filename, 0, 200); // Limit length
        }

        return 'product_' . str_pad((string)$index, 5, '0', STR_PAD_LEFT);
    }
}
