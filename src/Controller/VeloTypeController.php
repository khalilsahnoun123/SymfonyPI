<?php

namespace App\Controller;

use App\Entity\VeloType;
use App\Form\VeloTypeType;
use App\Repository\VeloTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/velo/type')]
final class VeloTypeController extends AbstractController
{
    #[Route(name: 'app_velo_type_index', methods: ['GET'])]
    public function index(VeloTypeRepository $veloTypeRepository): Response
    {
        return $this->render('Admin/velo_type/index.html.twig', [
            'velo_types' => $veloTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_velo_type_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $veloType = new VeloType();
    $form = $this->createForm(VeloTypeType::class, $veloType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('velo_image')->getData();
        
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                
                $veloType->setVeloImage('/uploads/'.$newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Image upload failed: '.$e->getMessage());
            }
        }

        $entityManager->persist($veloType);
        $entityManager->flush();
        
        $this->addFlash('success', 'New bike type created!');
        return $this->redirectToRoute('app_velo_type_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('Admin/velo_type/new.html.twig', [
        'velo_type' => $veloType,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_type}', name: 'app_velo_type_show', methods: ['GET'])]
    public function show(VeloType $veloType): Response
    {
        return $this->render('Admin/velo_type/show.html.twig', [
            'velo_type' => $veloType,
        ]);
    }

    // src/Controller/VeloTypeController.php
#[Route('/{id_type}/edit', name: 'app_velo_type_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, VeloType $veloType, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $form = $this->createForm(VeloTypeType::class, $veloType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('velo_image')->getData();
        
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                
                // Remove old image if exists
                if ($veloType->getVeloImage()) {
                    $oldImage = $this->getParameter('uploads_directory').'/'.basename($veloType->getVeloImage());
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                
                $veloType->setVeloImage('/uploads/'.$newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Image upload failed: '.$e->getMessage());
            }
        }

        $entityManager->flush();
        $this->addFlash('success', 'Bike type updated successfully!');
        return $this->redirectToRoute('app_velo_type_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('Admin/velo_type/edit.html.twig', [
        'velo_type' => $veloType,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_type}', name: 'app_velo_type_delete', methods: ['POST'])]
    public function delete(Request $request, VeloType $veloType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$veloType->getId_type(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($veloType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_velo_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
