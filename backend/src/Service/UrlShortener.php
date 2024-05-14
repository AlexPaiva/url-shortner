<?php

namespace App\Service;

use App\Repository\ShortUrlRepository;

class UrlShortener
{
    private ShortUrlRepository $repository;

    public function __construct(ShortUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function generateShortCode(): string
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 6)), 0, 6);
    }

    public function resolveShortCode(string $shortCode): ?string
    {
        $shortUrl = $this->repository->findOneBy(['shortCode' => $shortCode]);
        return $shortUrl ? $shortUrl->getOriginalUrl() : null;
    }
}

