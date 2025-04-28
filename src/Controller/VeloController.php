<?php // src/Controller/VeloController.php
namespace App\Controller;

use App\Entity\Velo;
use App\Entity\Stationvelo;
use App\Form\VeloType;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VeloController extends AbstractController
{
    #[Route('/station/{id}/velos', name: 'app_station_velos')]
    public function index(Stationvelo $station, VeloRepository $veloRepository): Response
    {
        $velos = $veloRepository->findBy(['stationvelo' => $station]);
        
        return $this->render('Admin/velo/index.html.twig', [
            'station' => $station,
            'velos' => $velos,
        ]);
    }

    #[Route('/velo/new/{stationId}', name: 'app_velo_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, int $stationId): Response
    {
        $velo = new Velo();
        $station = $entityManager->getRepository(Stationvelo::class)->find($stationId);
        $velo->setStationvelo($station);
        
        $form = $this->createForm(VeloType::class, $velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($velo);
            $entityManager->flush();

            return $this->redirectToRoute('app_station_velos', ['id' => $stationId]);
        }

        return $this->render('Admin/velo/new.html.twig', [
            'form' => $form->createView(),
            'station' => $station,
        ]);
    }

    #[Route('/velo/{id}/edit', name: 'app_velo_edit')]
    public function edit(Request $request, Velo $velo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VeloType::class, $velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_station_velos', ['id' => $velo->getStationvelo()->getIdStation()]);
        }

        return $this->render('Admin/velo/edit.html.twig', [
            'form' => $form->createView(),
            'velo' => $velo,
            'station' => $velo->getStationvelo(),
        ]);
    }

    #[Route('/velo/{id}', name: 'app_velo_delete', methods: ['POST'])]
    public function delete(Request $request, Velo $velo, EntityManagerInterface $entityManager): Response
    {
        $stationId = $velo->getStationvelo()->getIdStation();
        
        if ($this->isCsrfTokenValid('delete'.$velo->getId_velo(), $request->request->get('_token'))) {
            $entityManager->remove($velo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_station_velos', ['id' => $stationId]);
    }
}