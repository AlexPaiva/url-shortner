<?php
namespace App\Tests\Controller;

use App\Repository\ShortUrlRepository;
use App\Service\UrlShortener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class UrlShortenerControllerTest extends WebTestCase
{
    private $client;
    private $repositoryMock;
    private $urlShortener;
    private $validatorMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->repositoryMock = $this->createMock(ShortUrlRepository::class);
        $this->urlShortener = new UrlShortener($this->repositoryMock);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);

        //Mocking services in the container
        self::getContainer()->set(ShortUrlRepository::class, $this->repositoryMock);
        self::getContainer()->set(ValidatorInterface::class, $this->validatorMock);
        self::getContainer()->set(UrlShortener::class, $this->urlShortener);
    }

    //Test case for successful URL shortening
    public function testShortenUrlSuccess()
    {
        $this->validatorMock->method('validate')->willReturn(new ConstraintViolationList());
        $this->repositoryMock->expects($this->once())->method('add');

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => 'https://example.com']));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('shortCode', $data);
        $this->assertArrayHasKey('shortUrl', $data);
    }

    //Test case for invalid URL shortening
    public function testShortenUrlInvalid()
    {
        $violations = new ConstraintViolationList();
        $violations->add(new \Symfony\Component\Validator\ConstraintViolation('Invalid URL', null, [], '', '', ''));
        $this->validatorMock->method('validate')->willReturn($violations);

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => 'invalid-url']));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    //Test case for empty URL input
    public function testShortenUrlEmpty()
    {
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => '']));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    //Test case for URL with special characters
    public function testShortenUrlWithSpecialCharacters()
    {
        $this->validatorMock->method('validate')->willReturn(new ConstraintViolationList());
        $this->repositoryMock->expects($this->once())->method('add');

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => 'https://example.com/?q=search&sort=desc']));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('shortCode', $data);
        $this->assertArrayHasKey('shortUrl', $data);
    }

    //Test case for invalid JSON input
    public function testShortenUrlWithInvalidJson()
    {
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], '{url:"invalid-url"}');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    //Test case for request without URL key
    public function testShortenUrlWithoutUrlKey()
    {
        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['invalid_key' => 'https://example.com']));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    //Test case for input sanitization
    public function testInputSanitization()
    {
        $this->validatorMock->method('validate')->willReturn(new ConstraintViolationList());
        $this->repositoryMock->expects($this->once())->method('add');

        $url = 'https://example.com/<script>alert("xss")</script>';
        $sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);

        $this->client->request('POST', '/shorten', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['url' => $url]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('shortCode', $data);
        $this->assertArrayHasKey('shortUrl', $data);
        $this->assertStringStartsWith('http', $data['shortUrl']);
    }
}
