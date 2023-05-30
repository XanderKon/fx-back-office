<?php

namespace App\Command;

use App\Service\Exchange\Action\ValidateCurrencyForExistAction;
use App\Service\Exchange\ExchangeFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:exchange',
    description: 'Make exchange',
)]
class ExchangeCommand extends Command
{
    private const INTERNAL_NAME = 'exchange';

    public function __construct(
        protected ValidateCurrencyForExistAction $validateCurrencyForExistAction,
        protected ExchangeFacade $exchangeFacade,
        protected Stopwatch $stopwatch,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to exchange')
            ->addArgument('from', InputArgument::REQUIRED, 'Exchange currency from')
            ->addArgument('to', InputArgument::REQUIRED, 'Exchange currency to');
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if (true === $input->mustSuggestArgumentValuesFor('from')) {
            $suggestions->suggestValues(['json', 'xml']);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stopwatch->start(self::INTERNAL_NAME);
        $io = new SymfonyStyle($input, $output);
        $amount = $this->floatValue($input->getArgument('amount'));

        if (0.0 === $amount) {
            $io->error('There is something wrong with amount! 0 will always be 0. Let\'s try something else!');

            return Command::INVALID;
        }

        $from = mb_strtoupper($input->getArgument('from'));
        $to = mb_strtoupper($input->getArgument('to'));

        if (!$this->validateCurrencyForExistAction->handle($from, $to)) {
            $io->error(
                sprintf(
                    'One or all currencies from your request [%s, %s] does not exist in our system. Sorry',
                    $from,
                    $to
                )
            );

            return Command::INVALID;
        }

        $result = $this->exchangeFacade->handle(floatval($amount), $from, $to);
        $this->stopwatch->stop(self::INTERNAL_NAME);
        $event = $this->stopwatch->getEvent(self::INTERNAL_NAME);

        $io->success("$amount $from = $result $to");
        $io->info("It took {$event->getDuration()} ms and {$event->getMemory()} bytes");

        return Command::SUCCESS;
    }

    private function floatValue(string $val): float
    {
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);

        return floatval($val);
    }
}
