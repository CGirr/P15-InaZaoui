<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setName('Ina Zaoui');
        $admin->setEmail('ina@zaoui.com');
        $admin->setPassword($this->hasher->hashPassword($admin, '123456'));
        $admin->setDescription('Ina Zaoui est une photographe globe-trotteuse, réputée pour son engagement à explorer les paysages du monde entier en utilisant exclusivement des moyens non motorisés tels que la marche, le vélo ou la voile. ');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setBlocked(false);
        $manager->persist($admin);

        // Guest actif
        $guest = new User();
        $guest->setName('Isydia');
        $guest->setEmail('isy@isy.com');
        $guest->setPassword($this->hasher->hashPassword($guest, '123456'));
        $guest->setDescription("Le maître de l'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l'énergie des rues dans des instants éblouissants.");
        $guest->setRoles(['ROLE_USER']);
        $guest->setBlocked(false);
        $manager->persist($guest);

        //Guest bloqué
        $blockedGuest = new User();
        $blockedGuest->setName('Cora G');
        $blockedGuest->setEmail('cora.g@gmail.com');
        $blockedGuest->setPassword($this->hasher->hashPassword($blockedGuest, '123456'));
        $blockedGuest->setDescription("Le maître de l'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l'énergie des rues dans des instants éblouissants.");
        $blockedGuest->setRoles(['ROLE_USER']);
        $blockedGuest->setBlocked(true);
        $manager->persist($blockedGuest);

        // Album
        $album = new Album();
        $album->setName('Nature');
        $manager->persist($album);

        // Media admin
        $media1 = new Media();
        $media1->setTitle('Photo Admin');
        $media1->setPath('uploads/0001.jpg');
        $media1->setUser($admin);
        $media1->setAlbum($album);
        $manager->persist($media1);

        // Media guest
        $media2 = new Media();
        $media2->setTitle('Photo Invité');
        $media2->setPath('uploads/0002.jpg');
        $media2->setUser($guest);
        $manager->persist($media2);

        // Media guest bloqué
        $media3 = new Media();
        $media3->setTitle('Photo Bloqué');
        $media3->setPath('uploads/0003.jpg');
        $media3->setUser($blockedGuest);
        $manager->persist($media3);

        $manager->flush();
    }
}
