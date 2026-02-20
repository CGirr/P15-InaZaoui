<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Album;

class AlbumControllerTest extends AbstractAdminTest
{
    public function testAdminCanAccessAlbumList(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/album');
        $this->assertResponseIsSuccessful();
    }

    public function testGuestCannotAccessAlbumList(): void
    {
        $this->loginAsUser();
        $this->client->request('GET', '/admin/album');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanAddAlbum(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/album/add');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Ajouter', [
            'album[name]' => 'Paysages',
        ]);
        $this->assertResponseRedirects('/admin/album');
    }

    public function testAdminCanEditAlbum(): void
    {
        $this->loginAsAdmin();
        $album = $this->getEntity(Album::class, ['name' => 'Nature']);
        $this->client->request('GET', '/admin/album/update/' . $album->getId());
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Modifier', [
            'album[name]' => 'Nature modifié',
        ]);
        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('td', "Nature modifié");
    }

    public function testAdminCanDeleteAlbum(): void
    {
        $this->loginAsAdmin();
        $album = $this->getEntity(Album::class, ['name' => 'Nature']);
        $this->client->request('GET', '/admin/album/delete/' . $album->getId());
        $this->assertResponseRedirects('/admin/album');
    }

    public function testGuestCannotAddAlbum(): void
    {
        $this->loginAsUser();
        $this->client->request('GET', '/admin/album/add');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testGuestCannotEditAlbum(): void
    {
      $this->loginAsUser();
      $album = $this->getEntity(Album::class, ['name' => 'Nature']);
      $this->client->request('GET', '/admin/album/update/' . $album->getId());
      $this->assertResponseStatusCodeSame(403);
    }

    public function testGuestCannotDeleteAlbum(): void
    {
        $this->loginAsUser();
        $album = $this->getEntity(Album::class, ['name' => 'Nature']);
        $this->client->request('GET', '/admin/album/delete/' . $album->getId());
        $this->assertResponseStatusCodeSame(403);
    }
}
