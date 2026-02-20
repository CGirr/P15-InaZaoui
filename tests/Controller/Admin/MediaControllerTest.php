<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Media;
use App\Tests\Controller\Admin\AbstractAdminTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaControllerTest extends AbstractAdminTest
{
    public function testAdminSeesAllMedias(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Photo Admin');
        $this->assertSelectorTextContains('body', 'Photo Invité');
        $this->assertSelectorTextContains('body', 'Photo Bloqué');
    }

    public function testGuestSeesOnlyOwnMedias(): void
    {
        $this->loginAsUser();
        $this->client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Photo Invité');
        $this->assertSelectorTextNotContains('body', 'Photo Admin');
        $this->assertSelectorTextNotContains('body', 'Photo Bloqué');
    }

    public function testAdminCanAddMedia(): void
    {
        $this->loginAsAdmin();

        // Créer une image temporaire
        $path = sys_get_temp_dir() . '/test.jpg';
        imagejpeg(imagecreatetruecolor(10, 10), $path);

        $file = new UploadedFile($path, 'test.jpg', 'image/jpeg', null, true);

        $this->client->request('GET', '/admin/media/add');
        $this->client->submitForm('Ajouter', [
            'media[title]' => 'Photo test',
            'media[file]' => $file,
        ]);
        $this->assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Photo test');
    }

    public function testAdminCanDeleteAnyMedia(): void
    {
        $this->loginAsAdmin();
        $media = $this->getEntity(Media::class, ['title' => 'Photo Admin']);
        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        $this->assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
        $this->assertSelectorTextNotContains('body', 'Photo Admin');
    }

    public function testGuestCannotDeleteOtherUserMedia(): void
    {
        $this->loginAsUser();
        $media = $this->getEntity(Media::class, ['title' => 'Photo Admin']);
        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testGuestCanDeleteOwnMedia(): void
    {
        $this->loginAsUser();
        $media = $this->getEntity(Media::class, ['title' => 'Photo Invité']);
        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        $this->assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
        $this->assertSelectorTextNotContains('body', 'Photo Invité');
    }

    public function testUploadInvalidFileType(): void
    {
        $this->loginAsAdmin();

        $path = sys_get_temp_dir() . '/test.txt';
        file_put_contents($path, 'texte de test');

        $this->client->request('GET', '/admin/media/add');
        $this->client->submitForm('Ajouter', [
            'media[title]' => 'Test',
            'media[file]' => $path,
        ]);
        $this->assertSelectorTextContains('.invalid-feedback', "Le format de l'image doit être valide (JPEG, PNG, WEBP)");
    }
}
