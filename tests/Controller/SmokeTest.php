<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function PHPUnit\Framework\assertStringContainsString;

class SmokeTest extends WebTestCase
{

    public function testApiDocUrlIsSuccessful(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);
        $client->request('GET', '/api/doc');

        self::assertResponseIsSuccessful();
    }

    public function testApiAccountUrlIsSecure(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);
        $client->request('POST', '/api/login');

        self::assertResponseStatusCodeSame(401);
    }



    public function testLogonRouteCanConnectAValidUser(): void
    {
        $client = self::createClient();
        
        $client->request('POST', '/api/login', [], [], [
'CONTENT_TYPE' => 'application/json',
        ],
         json_encode ([
    'username' => 'email.1@ecf.fr',
    'password' => 'password$i',
], JSON_THROW_ON_ERROR));

        $statusCode = $client->getResponse()->getStatusCode();
        dd($statusCode);
        $this->assertEquals(200, $statusCode);
        $content = $client->getResponse()->getContent();
        $this->assertStringContainsString('user', $content);
        dd($content);
    }





}
