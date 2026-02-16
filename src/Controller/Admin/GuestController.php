<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class GuestController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/admin/guest', name: 'app_guest')]
    public function index(): Response
    {
        $guests = $this->entityManager->getRepository(User::class)->findBy(
            [],
            ['name' => 'ASC']
        );

        // Exclude admin form the guests list
        $guests = array_filter($guests, fn (User $user) => !in_array('ROLE_ADMIN', $user->getRoles()));

        return $this->render('admin/guest/index.html.twig', [
                'guests' => $guests,
        ]);
    }
}
