<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\SetRepository;
use App\Service\PokemonDatabaseService;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsCommand(
    name: 'app:import-cards-api',
    description: 'Import cards data from external API',
)]
#[AsPeriodicTask(
    frequency: '3 minutes',
    schedule: 'update_cards_database'
)]
final class ImportCardsApiCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PokemonDatabaseService $service,
        private readonly SetRepository $setRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('set', null, InputOption::VALUE_OPTIONAL, 'Only update cards for a specific set.')
            ->addOption('skipSetsUpdate', null, InputOption::VALUE_NONE, 'Skips Sets update.')
        ;
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->warning('Import cards database started.');

        $io = new SymfonyStyle($input, $output);
        $set = $input->getOption('set');

        if (!empty($set)) {
            /* @phpstan-ignore encapsedStringPart.nonString */
            $io->note("You passed an argument: {$set}");
        }
        // Sets update
        $setsMessage = 'You skipped sets update.';
        if (!$input->getOption('skipSetsUpdate')) {
            $sets = $this->service->getAllSets();
            $setsMessage = "It was updated with {$sets->count()} sets.";
        }
        $io->note($setsMessage);

        // Cards update
        $cardsUpdatedCount = 0;
        $sets = match (true) {
            empty($set) => $this->setRepository->findAll(),
            default => $this->setRepository->findBy(['id' => $set]),
        };
        $cardsMessage = 'No cards were updated.';
        foreach ($sets as $key => $set) {
            $num = $key + 1;
            $io->note("Updating cards for set #{$num}: '{$set->getId()}' - '{$set->getName()}'");
            $cards = $this->service->getCardsBySet($set);
            $io->note("It was updated with {$cards->count()} cards.");
            $cardsUpdatedCount += $cards->count();
            $cardsMessage = "It was updated a total of {$cardsUpdatedCount} cards.";
        }
        $io->note($cardsMessage);
        $io->success('Import successfully completed.');
        $this->logger->warning('Import cards database finished.');

        return Command::SUCCESS;
    }
}
