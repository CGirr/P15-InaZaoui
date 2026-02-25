<?php

namespace App\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginPage(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithValidCredentials(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'ina@zaoui.com',
            '_password' => '123456',
        ]);
        $this->assertResponseRedirects('/');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'ina@zaoui.com',
            '_password' => '12345',
        ]);
        $this->assertResponseRedirects('/login');
    }

    public function testLoginWithBlockedUser(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'cora.g@gmail.com',
            '_password' => '123456',
        ]);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger',"Votre compte a été bloqué, veuillez contacter l'administrateur");
    }

    public function testUnauthenticatedUserIsRedirectedToLogin(): void
    {
        $this->client->request('GET', '/admin/guest');
        $this->assertResponseRedirects('/login');
    }
}
