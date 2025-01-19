<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CardsController extends AbstractController
{
    #[Route('/cards', name: 'cards_index')]
    public function index(): Response
    {
        return $this->render('cards/index.html.twig', [
            'cards' => [],
        ]);
    }
}
