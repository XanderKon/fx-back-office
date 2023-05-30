<?php

namespace App\Command;

use App\Enum\RedisEnum;
use App\Service\Import\ImportFacade;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsCommand(
    name: 'app:import-rate',
    description: 'Console command for importing Rates from all or concrete Source if indicate',
)]
class ImportRateCommand extends Command
{
    private const INTERNAL_NAME = 'import-data';

    public function __construct(
        protected ImportFacade $importFacade,
        protected TagAwareCacheInterface $cache,
        protected Stopwatch $stopwatch,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::IS_ARRAY, 'Argument description')
        ;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stopwatch->start(self::INTERNAL_NAME);
        $io = new SymfonyStyle($input, $output);
        $arg1 = (array) $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', json_encode($arg1)));
        }

        $this->importFacade->handle();

        // invalidate cache
        $this->cache->invalidateTags([RedisEnum::TAG_INVALIDATE_BY_IMPORT->value]);

        $this->stopwatch->stop(self::INTERNAL_NAME);
        $event = $this->stopwatch->getEvent(self::INTERNAL_NAME);

        $io->success('Import operation has been completed!');
        $io->info("It took {$event->getDuration()} ms and {$event->getMemory()} bytes");

        return Command::SUCCESS;
    }
}
