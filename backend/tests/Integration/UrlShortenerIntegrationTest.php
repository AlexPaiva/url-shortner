<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\ShortUrl;
use Doctrine\ORM\EntityManagerInterface;

class UrlShortenerIntegrationTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        //Close the database connection after each test
        $this->entityManager->close();
        $this->entityManager = null;
    }

    
    //Generate a random URL for testing
    private function generateRandomUrl(): string
    {
        $randomString = bin2hex(random_bytes(5));
        return "https://example.com/{$randomString}";
    }

    
    //Test shortening a URL and then redirecting to it
    public function testShortenAndRedirect()
    {
        $randomUrl = $this->generateRandomUrl();

        //Test URL shortening
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => $randomUrl]));
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $shortenResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('shortCode', $shortenResponse);
        $this->assertArrayHasKey('shortUrl', $shortenResponse);

        $shortCode = $shortenResponse['shortCode'];

        //Verify the entry in the database
        $shortUrl = $this->entityManager->getRepository(ShortUrl::class)->findOneBy(['shortCode' => $shortCode]);
        $this->assertNotNull($shortUrl);
        $this->assertEquals($randomUrl, $shortUrl->getOriginalUrl());

        //Test redirection
        $this->client->request('GET', '/' . $shortCode);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($randomUrl));
    }

    
    //Test submitting an invalid URL
    public function testInvalidUrl()
    {
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => 'invalid-url']));
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());

        $errorResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $errorResponse);
        $this->assertStringContainsString('The URL "invalid-url" is not a valid URL.', $errorResponse['error']);
    }

    
    //Test submitting an empty URL
    public function testEmptyUrl()
    {
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => '']));
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());

        $errorResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $errorResponse);
        $this->assertEquals('URL is required', $errorResponse['error']);
    }

    
    //Test shortening and redirecting a URL with special characters
    public function testUrlWithSpecialCharacters()
    {
        $randomUrl = $this->generateRandomUrl() . '/!@#$%^&*()';

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => $randomUrl]));
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $shortenResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('shortCode', $shortenResponse);
        $this->assertArrayHasKey('shortUrl', $shortenResponse);

        $shortCode = $shortenResponse['shortCode'];

        //Verify the entry in the database
        $shortUrl = $this->entityManager->getRepository(ShortUrl::class)->findOneBy(['shortCode' => $shortCode]);
        $this->assertNotNull($shortUrl);
        $this->assertEquals($randomUrl, $shortUrl->getOriginalUrl());

        //Test redirection
        $this->client->request('GET', '/' . $shortCode);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($randomUrl));
    }

    
    //Test shortening and redirecting a URL with query parameters
    public function testUrlWithQueryParameters()
    {
        $randomUrl = $this->generateRandomUrl() . '/?param1=value1&param2=value2';

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => $randomUrl]));
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $shortenResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('shortCode', $shortenResponse);
        $this->assertArrayHasKey('shortUrl', $shortenResponse);

        $shortCode = $shortenResponse['shortCode'];

        //Verify the entry in the database
        $shortUrl = $this->entityManager->getRepository(ShortUrl::class)->findOneBy(['shortCode' => $shortCode]);
        $this->assertNotNull($shortUrl);
        $this->assertEquals($randomUrl, $shortUrl->getOriginalUrl());

        //Test redirection
        $this->client->request('GET', '/' . $shortCode);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($randomUrl));
    }
}

