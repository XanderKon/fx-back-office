<?php

namespace App\Controller;

use App\Repository\RateRepository;
use App\Request\ExchangeRequest;
use App\Service\Exchange\ExchangeFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class RateController extends AbstractController
{
    #[Route('/rate', name: 'app_get_rate', methods: ['GET'])]
    public function get(RateRepository $rateRepository): Response
    {
        return $this->json($rateRepository->findAllAvailableCurrencies());
    }

    #[Route('/rate', name: 'app_post_rate', methods: ['POST'])]
    public function post(
        #[MapRequestPayload] ExchangeRequest $exchangeRequest,
        ExchangeFacade $exchangeFacade
    ): Response {
        return $this->json(
            $exchangeFacade->handle(
                $exchangeRequest->amount,
                $exchangeRequest->from,
                $exchangeRequest->to
            )
        );
    }
}
