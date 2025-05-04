<?php
namespace App\Controller\velo;

use App\Entity\velo\Stationvelo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\velo\StationveloFormType;
use App\Repository\velo\StationveloRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Knp\Component\Pager\PaginatorInterface;

class StationveloController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/stations', name: 'app_stations_index')]
    public function index(Request $request, StationveloRepository $stationveloRepository, PaginatorInterface $paginator): Response
    {
        $query = $stationveloRepository->createQueryBuilder('s')->getQuery();

        $stations = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3 // Number of stations per page
        );

        return $this->render('Admin/stationvelo/index.html.twig', [
            'stations' => $stations,
        ]);
    }

    #[Route('/stations/search', name: 'app_stations_search')]
public function searchStations(Request $request, StationveloRepository $stationveloRepository, PaginatorInterface $paginator): Response
{
    $name = $request->query->get('name_station');
    $gouvernera = $request->query->get('gouvernera');
    $municapilite = $request->query->get('municapilite');
    $adresse = $request->query->get('adresse');

    $qb = $stationveloRepository->createQueryBuilder('s');

    if ($name) {
        $qb->andWhere('s.name_station LIKE :name')->setParameter('name', "%$name%");
    }

    if ($gouvernera) {
        $qb->andWhere('s.gouvernera LIKE :gouv')->setParameter('gouv', "%$gouvernera%");
    }

    if ($municapilite) {
        $qb->andWhere('s.municapilite LIKE :mun')->setParameter('mun', "%$municapilite%");
    }

    if ($adresse) {
        $qb->andWhere('s.adresse LIKE :adr')->setParameter('adr', "%$adresse%");
    }

    $query = $qb->getQuery();

    $stations = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        3
    );

    foreach ($stations as $station) {
        $fullAddress = urlencode($station->getGouvernera() . ' ' . $station->getMunicapilite() . ' ' . $station->getAdresse());

        try {
            $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $fullAddress,
                    'format' => 'json'
                ],
                'headers' => [
                    'User-Agent' => 'SymfonyBikeApp/1.0'
                ]
            ]);

            $results = $response->toArray();

            if (!empty($results)) {
                $station->lat = $results[0]['lat'];
                $station->lon = $results[0]['lon'];
            } else {
                $station->lat = null;
                $station->lon = null;
            }
        } catch (\Exception $e) {
            $station->lat = null;
            $station->lon = null;
        }
    }

    return $this->render('Front/stationvelo/search.html.twig', [
        'stations' => $stations,
        'filters' => [
            'name_station' => $name,
            'gouvernera' => $gouvernera,
            'municapilite' => $municapilite,
            'adresse' => $adresse,
        ]
    ]);
}


        
            
            

    #[Route('/station/new', name: 'app_station_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Stationvelo();
        $form = $this->createForm(StationveloFormType::class, $station);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            return $this->redirectToRoute('app_stations_index');
        }

        return $this->render('Admin/stationvelo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/station/{id}/edit', name: 'app_station_edit')]
    public function edit(Request $request, Stationvelo $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationveloFormType::class, $station);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stations_index');
        }

        return $this->render('Admin/stationvelo/edit.html.twig', [
            'form' => $form->createView(),
            'station' => $station,
        ]);
    }

    #[Route('/station/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(Request $request, Stationvelo $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $station->getId_station(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stations_index');
    }

    #[Route('/stations/{id}/velos', name: 'velos_list')]
    public function listVelos(Stationvelo $station): Response
    {
        $velos = $station->getVelos()->filter(function ($velo) {
            return $velo->isDispo();
        });

        return $this->render('Front/stationvelo/velos.html.twig', [
            'station' => $station,
            'velos' => $velos,
        ]);
    }
}
