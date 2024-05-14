<?php

namespace App\Controller;

use App\Entity\ShortUrl;
use App\Repository\ShortUrlRepository;
use App\Service\UrlShortener;
use App\Validator\Constraints\ValidUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UrlShortenerController extends AbstractController
{
    private UrlShortener $urlShortener;
    private ShortUrlRepository $repository;
    private ValidatorInterface $validator;

    public function __construct(UrlShortener $urlShortener, ShortUrlRepository $repository, ValidatorInterface $validator)
    {
        $this->urlShortener = $urlShortener;
        $this->repository = $repository;
        $this->validator = $validator;
    }

    #[Route('/shorten', name: 'shorten_url', methods: ['POST'])]
    public function shorten(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $originalUrl = $data['url'] ?? null;

        $originalUrl = filter_var($originalUrl, FILTER_SANITIZE_URL);

        if (!$originalUrl) {
            return $this->json(['error' => 'URL is required'], Response::HTTP_BAD_REQUEST);
        }

        $violations = $this->validator->validate($originalUrl, [new ValidUrl()]);
        if (count($violations) > 0) {
            return $this->json(['error' => (string) $violations], Response::HTTP_BAD_REQUEST);
        }

        $shortCode = $this->urlShortener->generateShortCode();
        $shortUrl = new ShortUrl();
        $shortUrl->setOriginalUrl($originalUrl);
        $shortUrl->setShortCode($shortCode);
        $shortUrl->setCreatedAt(new \DateTime());

        $this->repository->add($shortUrl, true);

        return $this->json([
            'shortCode' => $shortCode,
            'shortUrl' => $this->generateUrl('redirect_to_original', ['shortCode' => $shortCode], 0)
        ]);
    }
}
