<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CardsController extends AbstractController
{
    #[Route('/cards', name: 'cards_index')]
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('cards/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    #[Route('/cards/{card}', name: 'cards_show')]
    public function show(Card $card): Response
    {
        return $this->render('cards/show.html.twig', ['card' => $card]);
    }
}
