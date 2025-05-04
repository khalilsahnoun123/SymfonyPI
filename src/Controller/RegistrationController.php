<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $recaptchaToken = $request->request->get('g-recaptcha-response');
            if (!$recaptchaToken) {
                $form->addError(new FormError('Please complete the reCAPTCHA.'));
            } else {
                $secret = $_ENV['EWZ_RECAPTCHA_SECRET'];
                $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptchaToken}");
                $responseData = json_decode($verifyResponse);
                if (!$responseData->success) {
                    $form->addError(new FormError('reCAPTCHA verification failed. Please try again.'));
                }
            }
            // Validate password confirmation
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('Passwords do not match.'));
            }
            if ($form->isValid()) {
                // Hash password
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
            
                // ✅ Set defaults for system-controlled fields
                $user->setRoles(['ROLE_USER']);
                $user->setNom($form->get('firstName')->getData(). '_' .$form->get('lastName')->getData());
                $user->setIsVerified(true);
                $user->setStatus('active');
                $user->setBloque('non');    
            
                // Save
                $entityManager->persist($user);
                $entityManager->flush();
           /* 
                // Send confirmation email
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('projet@gmail.com', 'Web Admin'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
           */ 
                $this->addFlash('success', 'Account created! Please check your email.');
            
                return $this->redirectToRoute('app_login');
            }
            
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans(
                $exception->getReason(),
                [],
                'VerifyEmailBundle'
            ));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
