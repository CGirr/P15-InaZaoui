<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class GuestController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * @return Response
     */
    #[Route('/admin/guest', name: 'admin_guest_index')]
    public function index(): Response
    {
        $guests = $this->entityManager->getRepository(User::class)->findBy(
            [],
            ['name' => 'ASC']
        );

        // Exclude admin from the guests list
        $guests = array_filter($guests, fn (User $user) => !in_array('ROLE_ADMIN', $user->getRoles()));

        return $this->render('admin/guest/index.html.twig', [
                'guests' => $guests,
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/admin/guest/add', name: 'admin_guest_add')]
    public function add(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $guest = new User();
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setPassword($hasher->hashPassword($guest, $guest->getPassword()));
            $guest->setRoles(['ROLE_USER']);
            $this->entityManager->persist($guest);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/add.html.twig', [
            'form' => $form->createView()]);
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/admin/guest/block/{id}', name: 'admin_guest_block')]
    public function block(User $user): Response
    {
        $user->setBlocked(!$user->isBlocked());
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/admin/guest/delete/{id}', name: 'admin_guest_delete')]
    public function delete(User $user): Response
    {
        // Deletes user's images from the disk
        foreach ($user->getMedias() as $media) {
            if (file_exists($media->getPath())) {
                unlink($media->getPath());
            }
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}
