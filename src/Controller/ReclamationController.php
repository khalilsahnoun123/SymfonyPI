<?php

namespace App\Controller;
use App\Form\EvaluationType;
use App\Entity\Reclamation;
use App\Service\NotificationService;
use App\Form\ReclamationType;
use App\Repository\TypeReclamationRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[Route('/admin/reclamation')]
class ReclamationController extends AbstractController
{
    
    

    #[Route('/user', name: 'user_reclamation_index', methods: ['GET'])]
    public function user(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('Front/reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/user/new', name: 'user_reclamation_new', methods: ['GET', 'POST'])]
    public function usernew(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
        {
            $reclamation = new Reclamation();
            $reclamation->setDateCreation(new \DateTime());
            $reclamation->setStatus('en attente');
            $reclamation->setUtilisateurId(1);

            $form = $this->createForm(ReclamationType::class, $reclamation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $attachmentFile = $form->get('attachment')->getData();

                if ($attachmentFile) {
                    $originalFilename = pathinfo($attachmentFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$attachmentFile->guessExtension();

                    $attachmentFile->move(
                        $this->getParameter('attachments_directory'), // Paramètre à définir dans config/services.yaml
                        $newFilename
                    );

                    $reclamation->setAttachment($newFilename);
                }

                $entityManager->persist($reclamation);
                $entityManager->flush();

                return $this->redirectToRoute('user_reclamation_index');
            }

            return $this->render('Front/reclamation/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }

    #[Route('/user/{id}/show', name: 'user_reclamation_show', methods: ['GET'])]
    public function userShow(Reclamation $reclamation): Response
    {
        return $this->render('Front/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_reclamation_edit', methods: ['GET', 'POST'])]
    public function userEdit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Ne permettre l’édition que si la réclamation est en attente
        if ($reclamation->getStatus() !== 'en attente') {
            $this->addFlash('warning', 'Vous ne pouvez modifier une réclamation que si elle est en attente.');
            return $this->redirectToRoute('user_reclamation_index');
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation mise à jour avec succès.');
            return $this->redirectToRoute('user_reclamation_show', ['id' => $reclamation->getId()]);
        }

        return $this->render('Front/reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'user_reclamation_delete', methods: ['POST'])]
    public function userDelete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // L'utilisateur ne peut supprimer que si la réclamation est encore en attente
        if ($reclamation->getStatus() !== 'en attente') {
            $this->addFlash('warning', 'Vous ne pouvez supprimer une réclamation que si elle est en attente.');
            return $this->redirectToRoute('user_reclamation_index');
        }

        if ($this->isCsrfTokenValid('delete_user_reclamation_' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('');
        }

        return $this->redirectToRoute('user_reclamation_index');
    }

    #[Route('/reclamation/{id}/evaluer', name: 'user_reclamation_evaluer', methods: ['GET', 'POST'])]
    public function evaluer(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        if ($reclamation->getStatus() !== 'resolue') {
            throw $this->createAccessDeniedException('Vous ne pouvez évaluer qu\'une réclamation résolue.');
        }

        $form = $this->createForm(EvaluationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success', 'Merci pour votre évaluation.');
            return $this->redirectToRoute('user_reclamation_index');
        }

        return $this->render('Front/reclamation/evaluer.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }









    #[Route('/', name: 'admin_reclamation_index', methods: ['GET'])]
    public function index(
        ReclamationRepository $reclamationRepository,
        TypeReclamationRepository $typeReclamationRepository,  // Ajout du repository pour les types
        Request $request
    ): Response {
        // Récupération des filtres (si définis)
        $query = $request->query->get('query');
        $type = $request->query->get('type');
        $status = $request->query->get('status');

        // Construire la requête de recherche
        $reclamations = $reclamationRepository->findFiltered($query, $type, $status);

        // Récupérer tous les types de réclamations
        $typesReclamation = $typeReclamationRepository->findAll();

        // Définir les statuts possibles
        $statuts = ['en attente', 'resolue', 'en cours', 'refusée'];

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'typesReclamation' => $typesReclamation,
            'statuts' => $statuts,  // Passer les statuts à la vue
        ]);
    }




    #[Route('/new', name: 'admin_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%kernel.project_dir%/public/uploads/attachments')] string $attachmentsDirectory): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('admin_reclamation_index');
        }

        $file->move($attachmentsDirectory, $newFilename);

        return $this->render('admin/reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('admin/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MailerInterface $mailer, Reclamation $reclamation, EntityManagerInterface $entityManager ): Response
    {
            // Traitement de la requête POST
            if ($request->isMethod('POST')) {
                $status = $request->request->get('status');
                $reclamation->setStatus($status);
        
                // Si une réponse a été ajoutée par l'admin, on la met à jour
                $reponse = $request->request->get('reponse');
                $reclamation->setReponse($reponse);
        
              

                
                // Sauvegarder les modifications
                    $entityManager->flush();
                    $email = (new TemplatedEmail())
                    ->from(new Address('sahnoun.khalil78@gmail.com','wasalni sUPPORT'))            
                    ->to('benaissaaziz8@gmail.com')                  
                    ->subject('Mise à jour de votre réclamation')  
                    ->htmlTemplate('Admin/reclamation/emailToUser.html.twig') 
                    ->context([                               
                        'reclamation' => $reclamation,
                    ])
                ;
                $this->addFlash('success', 'Réclamation mise à jour avec succès.');
                        
                $mailer->send($email);
                    
                    
            }
       
        
            return $this->render('admin/reclamation/edit.html.twig', [
                'reclamation' => $reclamation,
            ]);
        }
    
    



    #[Route('/{id}/delete', name: 'admin_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_reclamation_index');
    }

}
