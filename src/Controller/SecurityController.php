<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();

            if (in_array('ROLE_COVOITUREUR', $roles, true)) {
                return $this->redirectToRoute('backoffice_covoitureur_dashboard');
            }

            if (in_array('ROLE_CHAUFFEUR_TAXI', $roles, true)) {
                return $this->redirectToRoute('backoffice_chauffeur_dashboard');
            }

            if (in_array('ROLE_VOYAGEUR', $roles, true)) {
                return $this->redirectToRoute('backoffice_voyageur_dashboard');
            }

            // Default redirection for ROLE_USER or fallback
            return $this->redirectToRoute('app_user_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class, [
            'email' => $lastUsername
        ]);

        $form->handleRequest($request);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the firewall.');
    }

}
