<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;

class GuestControllerTest extends AbstractAdminTest
{
    /**
     * @return void
     */
    public function testAdminCanAccessGuestsList(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/guest');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testGuestCannotAccessGuestsList(): void
    {
        $this->loginAsUser();
        $this->client->request('GET', '/admin/guest');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @return void
     */
    public function testAdminCanAddGuest(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Ajouter', [
            'guest[name]' => 'Invité test',
            'guest[email]' => 'test@test.com',
            'guest[password]' => '123456',
        ]);
        $this->assertResponseRedirects('/admin/guest');
        $this->client->followRedirect();
        $this->assertAnySelectorTextContains('td', 'Invité test');
    }

    /**
     * @return void
     */
    public function testAdminCannotAddGuestWithInvalidEmail(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Ajouter', [
            'guest[name]' => 'Invité test',
            'guest[email]' => 'test@test',
            'guest[password]' => '123456',
        ]);
        $this->assertSelectorTextContains('.invalid-feedback', "L'email n'est pas valide");
    }

    /**
     * @return void
     */
    public function testAdminCannotAddGuestWithShortPassword(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Ajouter', [
            'guest[name]' => 'Invité test',
            'guest[email]' => 'test@test.com',
            'guest[password]' => '1234',
        ]);
        $this->assertSelectorTextContains('.invalid-feedback', "Le mot de passe doit contenir au moins 6 caractères");
    }

    /**
     * @return void
     */
    public function testAdminCanBlockGuest(): void
    {
        $this->loginAsAdmin();
        $guest = $this->getEntity(User::class, ['email' => 'isy@isy.com']);
        $this->assertFalse($guest->isBlocked());

        $this->client->request('GET', '/admin/guest/block/' . $guest->getId());
        $this->assertResponseRedirects('/admin/guest');

        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->clear();
        $guest = $this->getEntity(User::class, ['email' => 'isy@isy.com']);
        $this->assertTrue($guest->isBlocked());
    }

    /**
     * @return void
     */
    public function testAdminCanUnblockGuest(): void
    {
        $this->loginAsAdmin();
        $guest = $this->getEntity(User::class, ['email' => 'cora.g@gmail.com']);
        $this->assertTrue($guest->isBlocked());

        $this->client->request('GET', '/admin/guest/block/' . $guest->getId());
        $this->assertResponseRedirects('/admin/guest');

        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->clear();
        $guest = $this->getEntity(User::class, ['email' => 'cora.g@gmail.com']);
        $this->assertFalse($guest->isBlocked());
    }

    /**
     * @return void
     */
    public function testAdminCanDeleteGuest(): void
    {
        $this->loginAsAdmin();
        $guest = $this->getEntity(User::class, ['email' => 'cora.g@gmail.com']);
        $this->client->request('GET', '/admin/guest/delete/' . $guest->getId());
        $this->assertResponseRedirects('/admin/guest');
        $this->client->followRedirect();
        $this->assertSelectorTextNotContains('body', 'Coraline Girr');
    }

    /**
     * @return void
     */
    public function testGuestCannotAddGuest(): void
    {
        $this->loginAsUser();
        $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @return void
     */
    public function testGuestCannotBlockGuest(): void
    {
        $this->loginAsUser();
        $guest = $this->getEntity(User::class, ['email' => 'cora.g@gmail.com']);
        $this->client->request('GET', '/admin/guest/block/' . $guest->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @return void
     */
    public function testGuestCannotDeleteGuest(): void
    {
        $this->loginAsUser();
        $guest = $this->getEntity(User::class, ['email' => 'cora.g@gmail.com']);
        $this->client->request('GET', '/admin/guest/delete/' . $guest->getId());
        $this->assertResponseStatusCodeSame(403);
    }
}
