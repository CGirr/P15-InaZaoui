<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/guests', name: 'guests')]
    public function guests(): Response
    {
        $guests = $this->em->getRepository(User::class)->findGuestsWithMediaCount();

        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    #[Route('/guest/{id}', name: 'guest')]
    public function guest(int $id): Response
    {
        $guest = $this->em->getRepository(User::class)->find($id);

        if (!$guest || $guest->isBlocked()) {
            throw $this->createNotFoundException();
        }

        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    /**
     * @param int|null $id
     * @return Response
     */
    #[Route('/portfolio/{id}', name: 'portfolio')]
    public function portfolio(?int $id = null): Response
    {
        $albums = $this->em->getRepository(Album::class)->findAll();
        $album = $id ? $this->em->getRepository(Album::class)->find($id) : null;
        $user = $this->em->getRepository(User::class)->findAdmin();

        $medias = $album
            ? $this->em->getRepository(Media::class)->findBy(['album' => $album])
            : $this->em->getRepository(Media::class)->findBy(['user' => $user]);
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}
