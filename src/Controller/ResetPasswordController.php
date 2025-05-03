<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = Uuid::v4();
                $user->setResetToken($token);
                $userRepository->save($user, true);

                $resetUrl = $this->generateUrl('app_reset_password', [
                    'token' => $token
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailMessage = (new Email())
                    ->from('noreply@wasalni.com')
                    ->to($user->getEmail())
                    ->subject('Reset Your Password')
                    ->html("<p>Click <a href='$resetUrl'>here</a> to reset your password.</p>");

                $mailer->send($emailMessage);
                $this->addFlash('success', 'If your email exists, a reset link was sent.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Invalid token');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $user->setResetToken(null);
            $userRepository->save($user, true);

            $this->addFlash('success', 'Password reset successful!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
