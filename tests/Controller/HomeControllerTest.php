<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @return void
     */
    public function testHomePage(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testPortfolioPage(): void
    {
        $this->client->request('GET', '/portfolio');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testPortfolioPageWithFilter(): void
    {
        $this->client->request('GET', '/portfolio/1');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testAboutPage(): void
    {
        $this->client->request('GET', '/about');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testGuestsListPage(): void
    {
        $this->client->request('GET', '/guests');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testBlockedGuestPageReturns404(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $blockedGuest = $em->getRepository(User::class)->findOneBy(['blocked' => true]);

        $this->client->request('GET', '/guest/' . $blockedGuest->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @return void
     */
    public function testActiveGuestPage(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $guest = $em->getRepository(User::class)->findOneBy(['email' => 'isy@isy.com']);

        $this->client->request('GET', '/guest/' . $guest->getId());
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testGuestsListExcludesBlockedGuest(): void
    {
        $this->client->request('GET', '/guests');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('h4:contains("Coraline Girr")');
        $this->assertSelectorExists('h4:contains("Isydia")');
    }
}
