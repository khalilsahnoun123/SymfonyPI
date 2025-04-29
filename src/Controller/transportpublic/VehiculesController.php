<?php

namespace App\Controller\transportpublic;

use App\Entity\transportpublic\Vehicules;
use App\Form\transportpublic\VehiculesType;
use App\Repository\transportpublic\VehiculesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/vehicules')]
class VehiculesController extends AbstractController
{
    #[Route('/', name: 'app_vehicules_index', methods: ['GET'])]
    public function index(VehiculesRepository $vehiculesRepository): Response
    {
        return $this->render('Admin/admin_transportPublic/vehicules/index.html.twig', [
            'vehicules' => $vehiculesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicules_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $vehicules = new Vehicules();
        $form = $this->createForm(VehiculesType::class, $vehicules);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vehicules);
            $em->flush();

            $this->addFlash('success', 'Véhicule créé avec succès !');
            return $this->redirectToRoute('app_vehicules_index');
        }

        return $this->render('Admin/admin_transportPublic/vehicules/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicules_show', methods: ['GET'])]
    public function show(Vehicules $vehicules): Response
    {
        return $this->render('Admin/admin_transportPublic/vehicules/show.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicules_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicules $vehicule, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VehiculesType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Véhicule mis à jour !');
            return $this->redirectToRoute('app_vehicules_index');
        }

        return $this->render('Admin/admin_transportPublic/vehicules/edit.html.twig', [
            'form' => $form->createView(),
            'vehicule' => $vehicule,
            
        ]);
    }

    #[Route('/{id}', name: 'app_vehicules_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicules $vehicules, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicules->getId(), $request->request->get('_token'))) {
            $em->remove($vehicules);
            $em->flush();
            $this->addFlash('success', 'Véhicule supprimé !');
        }
        return $this->redirectToRoute('app_vehicules_index');
    }
}