<?php
// src/Controller/LigneController.php

namespace App\Controller\transportpublic;

use App\Entity\transportpublic\Ligne;
use App\Form\transportpublic\LigneType;
use App\Repository\transportpublic\LigneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ligne')]
class LigneController extends AbstractController
{
    #[Route('/', name: 'app_ligne_index', methods: ['GET'])]
    public function index(Request $request, LigneRepository $ligneRepository): Response
    {
        $search = $request->query->get('query', '');

        if ($search !== '') {
            // call our new repository helper
            $lignes = $ligneRepository->findByName($search);
        } else {
            $lignes = $ligneRepository->findAll();
        }

        return $this->render('Admin/admin_transportPublic/ligne/ligne.html.twig', [
            'lignes' => $lignes,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_ligne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ligne = new Ligne();
        $form = $this->createForm(LigneType::class, $ligne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ligne);
            $em->flush();

            $this->addFlash('success', 'Ligne créée avec succès.');
            return $this->redirectToRoute('app_ligne_index');
        }

        return $this->render('Admin/admin_transportPublic/ligne/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_show', methods: ['GET'])]
    public function show(Ligne $ligne): Response
    {
        return $this->render('Admin/admin_transportPublic/ligne/show.html.twig', [
            'ligne' => $ligne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ligne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ligne $ligne, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LigneType::class, $ligne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Ligne mise à jour.');
            return $this->redirectToRoute('app_ligne_index');
        }

        return $this->render('Admin/admin_transportPublic/ligne/edit.html.twig', [
            'form' =>$form->createView(),
            'ligne' => $ligne,
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_delete', methods: ['POST'])]
    public function delete(Request $request, Ligne $ligne, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ligne->getId(), $request->request->get('_token'))) {
            $em->remove($ligne);
            $em->flush();
            $this->addFlash('success', 'Ligne supprimée.');
        }

        return $this->redirectToRoute('app_ligne_index');
    }
    #[Route('/{id}', name: 'app_ligne_show', methods: ['GET'])]
    public function stations(Ligne $ligne): Response
    {
        // Récupère les stations et vehicule associées à cette ligne
        $stations  = $ligne->getStations();
        $vehicules = $ligne->getVehicules(); 
    

        return $this->render('Admin/admin_transportPublic/ligne/show.html.twig', [
            'ligne'    => $ligne,
            'stations' => $stations,
            'vehicules'=>$vehicules,
        ]);
    }
}
