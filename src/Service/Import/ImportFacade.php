<?php

namespace App\Service\Import;

use App\Repository\SourceRepository;
use App\Service\Import\Exception\UndefinedImportProviderException;
use App\Service\Import\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ImportFacade
{
    /**
     * @param iterable<ProviderInterface> $providers
     */
    public function __construct(
        #[TaggedIterator('app.provider')]
        protected iterable $providers,
        protected readonly SaveImportDataService $saveImportDataService,
        private readonly SourceRepository $sourceRepository
    ) {
    }

    public function handle(): void
    {
        foreach ($this->providers as $provider) {
            if (!$provider instanceof ProviderInterface) {
                continue;
            }

            // Find Service from DB by title
            $source = $this->sourceRepository->getByTitle($provider->getProviderName());

            if (empty($source)) {
                throw new UndefinedImportProviderException($provider->getProviderName());
            }

            // Skip if inactive
            if (!$source->isIsActive()) {
                continue;
            }

            // Get clear import data from Service
            $importData = $provider->getData()->parseData();

            // Save import data to DB
            $this->saveImportDataService->handle($importData, $source);
        }
    }
}
