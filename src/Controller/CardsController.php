<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CardsController extends AbstractController
{
    public const MAX_RESULTS = 20;

    #[Route('/cards', name: 'cards_index')]
    public function index(CardRepository $cardRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $cards = $cardRepository->paginateCards($page, self::MAX_RESULTS);
        $maxPages = ceil($cards->getTotalItemCount() / self::MAX_RESULTS);

        return $this->render('cards/index.html.twig', compact('page', 'cards', 'maxPages'));
    }

    #[Route('/cards/{card}', name: 'cards_show')]
    public function show(Card $card, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('cards/show.html.twig', compact('card', 'page'));
    }
}
