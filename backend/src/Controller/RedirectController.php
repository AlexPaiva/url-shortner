<?php

namespace App\Controller;

use App\Repository\ShortUrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    private ShortUrlRepository $repository;

    public function __construct(ShortUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/{shortCode}', name: 'redirect_to_original', methods: ['GET'])]
    public function redirectToOriginal(string $shortCode): Response
    {
        // Sanitize input
        $shortCode = filter_var($shortCode, FILTER_SANITIZE_SPECIAL_CHARS);

        $shortUrl = $this->repository->findOneBy(['shortCode' => $shortCode]);

        if (!$shortUrl) {
            return $this->json(['error' => 'URL not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->redirect($shortUrl->getOriginalUrl());
    }
}
