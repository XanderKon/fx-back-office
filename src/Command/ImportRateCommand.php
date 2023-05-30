<?php

namespace App\Command;

use App\Service\Import\ImportFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-rate',
    description: 'Console command for importing Rates from all or concrete Source if indicate',
)]
class ImportRateCommand extends Command
{
    public function __construct(protected ImportFacade $importFacade, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::IS_ARRAY, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', json_encode($arg1)));
        }

        $this->importFacade->handle();

        return Command::SUCCESS;
    }
}
