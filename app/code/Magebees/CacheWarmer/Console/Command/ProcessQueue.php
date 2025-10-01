<?php

namespace Magebees\CacheWarmer\Console\Command;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessQueue extends AbstractSetupCommand
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Queue $queue,
        State $state,
        Config $config,
        $name = null
    ) {
        parent::__construct($name);

        $this->queue = $queue;
        $this->state = $state;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('magebees:warmcache')->setDescription('Starts the processing of the first batch of URLs.');
    }
	
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_GLOBAL,
            [$this, 'process'],
            [$input, $output]
        );
    }

    public function process(InputInterface $input, OutputInterface $output): int
    {
        try {
            if ($this->config->isModuleEnabled()) {
                $batchSize = $this->config->getBatchSize();
                $output->writeln("<info>Current batch size: $batchSize</info>");
                $output->writeln(__('<info>Warming up process starts...</info>'));
				$startTime = microtime(true);
                $crawledPages = $this->queue->process();
				$endTime = microtime(true);
                $output->writeln('');
                $output->writeln("<info>$crawledPages URLs has been successfully processed.</info>");
				$resultTime = intval($endTime - $startTime);
                $output->writeln('<info>Done in ' . gmdate('H:i:s', $resultTime) . '</info>');
                return Cli::RETURN_SUCCESS;
            } else {
                $output->writeln(
                    "<info>The warming queue cannot be generated and warmed up because the module is disabled.</info>"
                );
                return Cli::RETURN_SUCCESS;
            }
        } catch (\Exception $e) {
            $output->writeln("<error>Processing failed! Error: {$e->getMessage()}</error>");

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }
    }
}
