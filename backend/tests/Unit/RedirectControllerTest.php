<?php
namespace App\Tests\Controller;

use App\Repository\ShortUrlRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class RedirectControllerTest extends WebTestCase
{
    private $client;
    private $repositoryMock;
    private $validatorMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->repositoryMock = $this->createMock(ShortUrlRepository::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);

        //Mocking services in the container
        self::getContainer()->set(ShortUrlRepository::class, $this->repositoryMock);
        self::getContainer()->set(ValidatorInterface::class, $this->validatorMock);
    }

    //Test case for successful redirection
    public function testRedirectToOriginalSuccess()
    {
        $shortUrl = new \App\Entity\ShortUrl();
        $shortUrl->setOriginalUrl('https://example.com');
        $shortUrl->setShortCode('d0bGjz');

        $this->repositoryMock->method('findOneBy')->willReturn($shortUrl);

        $this->client->request('GET', '/d0bGjz');

        $this->assertTrue($this->client->getResponse()->isRedirect('https://example.com'));
    }

    //Test case for redirection to non-existent URL
    public function testRedirectToOriginalNotFound()
    {
        $this->repositoryMock->method('findOneBy')->willReturn(null);

        $this->client->request('GET', '/invalidCode');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    //Test case to check for SQL injection prevention
    public function testRedirectWithSqlInjection()
    {
        $shortUrl = new \App\Entity\ShortUrl();
        $shortUrl->setOriginalUrl('https://example.com');
        $shortUrl->setShortCode('1; DROP TABLE users');

        $this->repositoryMock->method('findOneBy')->willReturn($shortUrl);

        $this->client->request('GET', '/1; DROP TABLE users');

        $this->assertTrue($this->client->getResponse()->isRedirect('https://example.com'));
    }

    //Test case for input sanitization
    public function testInputSanitization()
    {
        $shortUrl = new \App\Entity\ShortUrl();
        $shortUrl->setOriginalUrl('https://example.com');
        $shortUrl->setShortCode('d0bGjz<script>alert("xss")</script>');
        $sanitizedShortCode = filter_var($shortUrl->getShortCode(), FILTER_SANITIZE_SPECIAL_CHARS);

        $this->repositoryMock->method('findOneBy')->willReturn($shortUrl);

        $this->client->request('GET', '/' . $sanitizedShortCode);
        $this->assertTrue($this->client->getResponse()->isRedirect('https://example.com'));
    }
}