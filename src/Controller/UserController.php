<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig');
    }
    #[Route('/statistics', name: 'app_user_statistics', methods: ['GET'])]
    public function statistics(UserRepository $userRepository): Response
    {
        // Example logic: get top 5 users with most "active" status
        $users = $userRepository->createQueryBuilder('u')
            ->select('u.firstName, u.lastName, COUNT(u.id) as activityCount')
            ->where('u.status = :status')
            ->setParameter('status', 'active')
            ->groupBy('u.email')
            ->orderBy('activityCount', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('user/statistics.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/search', name: 'app_user_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository): JsonResponse
    {
        $search = $request->query->get('search', '');
        $sortBy = $request->query->get('sortBy', 'firstName');
        $sortOrder = $request->query->get('sortOrder', 'asc');

        // Get current user to exclude from results
        $currentUser = $this->getUser();
        $currentUserId = $currentUser ? $currentUser->getId() : null;

        $users = $userRepository->searchAndSort($search, $sortBy, $sortOrder, $currentUserId);

        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = [
                'fullName' => $user->getFirstName() . ' ' . $user->getNom(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhoneNumber() ?: '-',
                'gouvernorat' => $user->getGouvernorat() ?: '-',
                'municipalite' => $user->getMunicipalite() ?: '-',
                'adresse' => $user->getAdresse() ?: '-',
                'roles' => $user->getRoles(),
                'isVerified' => $user->isVerified(),
                'status' => $user->getStatus(),
                'bloque' => $user->getBloque(),
                'actions' => [
                    'show' => $this->generateUrl('app_user_show', ['id' => $user->getId()]),
                    'edit' => $this->generateUrl('app_user_edit', ['id' => $user->getId()]),
                    'activate' => $this->generateUrl('app_user_activate', ['id' => $user->getId()]),
                    'deactivate' => $this->generateUrl('app_user_deactivate', ['id' => $user->getId()]),
                    'block' => $this->generateUrl('app_user_block', ['id' => $user->getId()]),
                    'unblock' => $this->generateUrl('app_user_unblock', ['id' => $user->getId()]),
                ]
            ];
        }

        return new JsonResponse($usersData);
    }

    #[Route('/{id}/activate', name: 'app_user_activate', methods: ['POST'])]
    public function activate(User $user, UserRepository $userRepository): JsonResponse
    {
        $user->setStatus('active');
        $user->setIsVerified(true);
        $userRepository->save($user, true);
        return new JsonResponse(['status' => 'success', 'message' => 'User activated successfully']);
    }

    #[Route('/{id}/deactivate', name: 'app_user_deactivate', methods: ['POST'])]
    public function deactivate(User $user, UserRepository $userRepository): JsonResponse
    {
        $user->setStatus('pending');
        $user->setIsVerified(false);
        $userRepository->save($user, true);
        return new JsonResponse(['status' => 'success', 'message' => 'User deactivated successfully']);
    }

    #[Route('/{id}/block', name: 'app_user_block', methods: ['POST'])]
    public function block(User $user, UserRepository $userRepository): JsonResponse
    {
        $user->setBloque('oui');
        $userRepository->save($user, true);
        return new JsonResponse(['status' => 'success', 'message' => 'User blocked successfully']);
    }

    #[Route('/{id}/unblock', name: 'app_user_unblock', methods: ['POST'])]
    public function unblock(User $user, UserRepository $userRepository): JsonResponse
    {
        $user->setBloque('non');
        $userRepository->save($user, true);
        return new JsonResponse(['status' => 'success', 'message' => 'User unblocked successfully']);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, ParameterBagInterface $params): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Hash password
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('password')->getData())
            );
            $user->setIsVerified(true);
            $user->setStatus('active');
            $user->setBloque('non');
            $selectedRole = $form->get('roles')->getData();
            $user->setRoles($selectedRole);
            if (!$selectedRole) {
                $user->setRoles(['ROLE_USER']);
            } else {
                $user->setRoles($selectedRole);
            }


            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadDir = $params->get('kernel.project_dir') . '/public/uploads/user_images';

                // Ensure the directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $uploadedFile->move($uploadDir, $newFilename);

                // Save path in entity (relative path)
                $user->setImage('uploads/user_images/' . $newFilename);
            }
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/profil', name: 'app_user_profil', methods: ['GET', 'POST'])]
    public function editProfil(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, ParameterBagInterface $params): Response
    {


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('password')->getData())) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $form->get('password')->getData())
                );
            }


            $user->setIsVerified(true);
            $user->setStatus('active');
            $user->setBloque('non');
            $selectedRole = $form->get('roles')->getData();
            $user->setRoles($selectedRole);
            if (!$selectedRole) {
                $user->setRoles('ROLE_USER');
            } else {
                $user->setRoles($selectedRole);
            }

            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadDir = $params->get('kernel.project_dir') . '/public/uploads/user_images';

                // Ensure the directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $uploadedFile->move($uploadDir, $newFilename);

                // Save path in entity (relative path)
                $user->setImage('uploads/user_images/' . $newFilename);
            }
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

}
