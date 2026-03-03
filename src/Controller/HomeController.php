<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AlbumRepository $albumRepository,
        private MediaRepository $mediaRepository,
    ) {}

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
     * @throws Exception
     */
    #[Route('/guests', name: 'guests')]
    public function guests(): Response
    {
        $guests = $this->userRepository->findGuestsWithMediaCount();

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
        $guest = $this->userRepository->find($id);

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
        $albums = $this->albumRepository->findAll();
        $album = $id ? $this->albumRepository->find($id) : null;
        $user = $this->userRepository->findAdmin();

        $medias = $album
            ? $this->mediaRepository->findBy(['album' => $album])
            : $this->mediaRepository->findBy(['user' => $user]);
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
