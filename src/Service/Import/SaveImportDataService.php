<?php

namespace App\Service\Import;

use App\Entity\Rate;
use App\Entity\Source;
use App\Service\DTO\RatesDTO;
use Doctrine\ORM\EntityManagerInterface;

class SaveImportDataService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(RatesDTO $currenciesDTO, Source $source): void
    {
        $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($currenciesDTO, $source) {
            foreach ($currenciesDTO->getValues() as $currency) {
                $rate = $em
                    ->getRepository(Rate::class)
                    ->findByBaseAndTargetAndSource(
                        $currency->getBase(),
                        $currency->getTarget(),
                        $source->getId()
                    )
                    ?: new Rate();

                $rate->setBase($currency->getBase());
                $rate->setTarget($currency->getTarget());
                $rate->setRate($currency->getRate());
                $rate->setSource($source);
                $em->persist($rate);
            }
        });
    }
}
