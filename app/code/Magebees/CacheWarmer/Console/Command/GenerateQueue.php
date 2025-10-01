<?php

namespace Magebees\CacheWarmer\Console\Command;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateQueue extends AbstractSetupCommand
{
    /**
     * @var Queue\RegenerateHandler
     */
    private $regenerateHandler;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Queue\RegenerateHandler $regenerateHandler,
        State $state,
        Config $config,
        $name = null
    ) {
        parent::__construct($name);
        $this->regenerateHandler = $regenerateHandler;
        $this->state = $state;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('magebees:warmqueue:generate')->setDescription('Run the warm cache queue generate command and create queue for warmer page url.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_GLOBAL,
            [$this, 'generate'],
            [$input, $output]
        );
    }

    public function generate(InputInterface $input, OutputInterface $output): int
    {
        try {
            if ($this->config->isModuleEnabled()) {
                $output->writeln('<info>Warmer queue generation starts...</info>');
                list($result, $items) = $this->regenerateHandler->execute(true);
                $output->writeln('');
                $output->writeln("<info>Cache warmer queue generate process finished for $items URLs.</info>");

                return Cli::RETURN_SUCCESS;
            } else {
                $output->writeln(
                    "<info>The warming queue cannot be generated and warmed up because the module is disabled.</info>"
                );
                return Cli::RETURN_SUCCESS;
            }
        } catch (\Exception $e) {
            $output->writeln("<error>Generation failed! Error: {$e->getMessage()}</error>");

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }
    }
}
