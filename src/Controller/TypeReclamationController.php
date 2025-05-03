<?php

namespace App\Controller;

use App\Entity\TypeReclamation;
use App\Form\TypeReclamationType;
use App\Repository\TypeReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/typereclamation')]
final class TypeReclamationController extends AbstractController
{
    #[Route(name: 'admin_typereclamation_index', methods: ['GET'])]
    public function index(TypeReclamationRepository $repo): Response
    {
        $types = $repo->findAll();

        return $this->render('Admin/typereclamation/index.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('/new', name: 'admin_typereclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $type = new TypeReclamation();
        $form = $this->createForm(TypeReclamationType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            return $this->redirectToRoute('admin_typereclamation_index');
        }

        return $this->render('Admin/typereclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('rec/{id}', name: 'admin_typereclamation_show', methods: ['GET'])]
    public function show(TypeReclamation $type): Response
    {
        return $this->render('Admin/reclamation/show.html.twig', [
            'type' => $type,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_typereclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeReclamation $type, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypeReclamationType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_typereclamation_index');
        }

        return $this->render('Admin/typereclamation/edit.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_typereclamation_delete', methods: ['POST'])]
    public function delete(Request $request, TypeReclamation $type, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $em->remove($type);
            $em->flush();
        }

        return $this->redirectToRoute('admin_typereclamation_index');
    }
 

}
