<?php

namespace App\Controller\taxi;

use App\Entity\taxi\Vehicule;
use App\Entity\taxi\ReservationTaxi;
use App\Form\taxi\VehiculeType;
use App\Repository\taxi\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;



#[Route('/vehicules')]
class VehiculeController extends AbstractController
{
    #[Route('/', name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('Admin/taxi/vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'immatriculation existe déjà
            $existingVehicule = $em->getRepository(Vehicule::class)->findOneBy(['immatriculation' => $vehicule->getImmatriculation()]);

            if ($existingVehicule) {
                // Afficher un message d'erreur si l'immatriculation existe déjà
                $this->addFlash('error', 'Cette immatriculation existe déjà. Veuillez en choisir une autre.');

                // Nous ne redirigeons pas ici, mais on renvoie la même vue avec le formulaire pour que l'utilisateur puisse corriger l'immatriculation
                return $this->render('Admin/taxi/vehicule/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Si l'immatriculation est unique, on persiste le véhicule
            $em->persist($vehicule);
            $em->flush();

            $this->addFlash('success', 'Véhicule créé avec succès !');
            return $this->redirectToRoute('app_vehicule_index');
        }

        return $this->render('Admin/taxi/vehicule/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('Admin/taxi/vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Véhicule mis à jour !');
            return $this->redirectToRoute('app_vehicule_index');
        }

        return $this->render('Admin/taxi/vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $em->remove($vehicule);
            $em->flush();
            $this->addFlash('success', 'Véhicule supprimé !');
        }
        return $this->redirectToRoute('app_vehicule_index');
    }


    #[Route('/front/vehicules', name: 'front_vehicules', methods: ['GET'])]
    public function frontList(VehiculeRepository $vehiculeRepository): Response
    {
        // Récupérer tous les véhicules
        $vehicules = $vehiculeRepository->findAll();

        // Vérifier s'il y a des véhicules
        if (empty($vehicules)) {
            $this->addFlash('error', 'Aucun véhicule n\'est disponible.');
        }

        return $this->render('Front/taxi/list.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/QrCode/generate/{id}', name: 'app_qr_code')]
    public function qrGenerator(ManagerRegistry $doctrine, $id, VehiculeRepository $repo)
    {
        $vehicule = $repo->find($id);
        // Ensure values are not null, and sanitize them
        $marque = htmlspecialchars($vehicule->getMarque() ?: 'No marque available', ENT_QUOTES, 'UTF-8');
        $modele = htmlspecialchars($vehicule->getModele() ?: 'No modele available', ENT_QUOTES, 'UTF-8');
        $immatriculation = htmlspecialchars($vehicule->getImmatriculation() ?: 'No immatriculation available', ENT_QUOTES, 'UTF-8');

        // Create the data string for the QR code
        $data = "Vehicule Details:\n";
        $data .= "Marque: {$marque}\n";
        $data .= "Modele: {$modele}\n";
        $data .= "Immatriculation: {$immatriculation}\n";
        $data .= "Enjoy your leisure time!";
        
        // Create a new QR code instance with the desired parameters
        $qrCode = new QrCode(
            $data,
            new Encoding('UTF-8'),  // Encoding
            ErrorCorrectionLevel::High,  // Error correction level
            300,  // Size
            10,  // Margin
            RoundBlockSizeMode::Margin,  // Round block size mode
            new Color(0, 0, 0),  // Foreground color (black)
            new Color(255, 255, 255)  // Background color (white)
        );

        // Create a writer to generate the PNG image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Return the PNG image as the response
        return new Response(
            $result->getString(),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );
    }
    
    
    

    




}